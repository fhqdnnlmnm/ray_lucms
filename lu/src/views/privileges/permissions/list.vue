<template>
    <div id="privileges-users-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入权限名称..." v-model="search.name"/>
            </Col>
            <Col span="2">
                <Button type="primary" icon="ios-search" @click="getList()">Search</Button>
            </Col>
            <Col span="2">
                <Button type="success" icon="plus" @click="addBtn()">Add</Button>
            </Col>
        </Row>
        <br>

        <Row>
            <Table border :columns="columns" :data="dataList" :loading="loading"></Table>
        </Row>

        <Modal v-model="addEditPermissionModal.show" :closable='false' :mask-closable=false :width="500">
            <h3 slot="header" style="color:#2D8CF0">编辑权限</h3>
            <Form ref="addEditPermissionForm"
                  :model="addEditPermissionForm" :label-width="100" label-position="right"
                  :rules="ruleAddEditPermission">
                <FormItem label="权限名称" prop="name">
                    <Input v-model="addEditPermissionForm.name" placeholder="请输入权限名称"></Input>
                </FormItem>
                <FormItem label="看守器" prop="guard_name">
                    <Input v-model="addEditPermissionForm.guard_name"
                           placeholder="请输入看守器"></Input>
                </FormItem>
                <FormItem label="权限描述" prop="description">
                    <Input v-model="addEditPermissionForm.description"
                           placeholder="请输入权限描述"></Input>
                </FormItem>

            </Form>
            <div slot="footer">
                <Button type="text" @click="cancelEditPass">取消</Button>
                <Button type="primary" @click="handleSubmit" :loading="addEditPermissionModal.saveLoading">保存</Button>
            </div>
        </Modal>
    </div>

</template>


<script>
    export default {
        data() {
            return {
                modalHeadImage: {
                    show: false,
                    url: null
                },
                loading: false,
                search: {
                    name: null,
                },
                addEditPermissionModal: {
                    show: false,
                    saveLoading: false,
                },
                ruleAddEditPermission: {
                    name: [
                        {required: true, message: '请填写权限名称', trigger: 'blur'}
                    ],
                    guard_name: [
                        {required: true, message: '请填写看守器', trigger: 'blur'}
                    ],
                },
                addEditPermissionForm: {
                    id: 0,
                    name: '',
                    guard_name: '',
                    description:'',
                },
                dataList: [],
                columns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: true,
                        width: 100
                    },
                    {
                        title: '权限名称',
                        key: 'name'
                    },
                    {
                        title: '看守器',
                        key: 'guard_name'
                    },
                    {
                        title: '权限描述',
                        key: 'description'
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        sortable: true,
                    },
                    {
                        title: '更新时间',
                        key: 'created_at'
                    },
                    {
                        title: '操作',
                        render: (h, params) => {
                            let t = this;
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'success',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => {
                                            t.addEditPermissionForm = t.dataList[params.index];
                                            t.addEditPermissionModal.show = true;
                                        }
                                    }

                                }, 'Edit'),
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除「'+params.row.name+'」权限？',
                                        transfer: true
                                    },
                                    on: {
                                        'on-ok': () => {
                                            t.deleteBtn(params.row.id, params.index);
                                        }
                                    }
                                }, [
                                    h('Button', {
                                        style: {
                                            margin: '0 5px'
                                        },
                                        props: {
                                            type: 'error',
                                            size: 'small',
                                            placement: 'top'
                                        }
                                    }, '删除'),
                                ])

                            ])
                        }
                    },
                ]
            }
        },

        created() {
            let t = this;
            t.getList()
        },
        methods: {
            cleanModal() {
                let t = this;
                t.addEditPermissionForm = {
                    id: 0,
                    name: '',
                    guard_name: '',
                    description:''
                };
            },
            handleSubmit() {
                let t = this;
                t.$refs.addEditPermissionForm.validate((valid) => {
                    if (valid) {
                        t.addEditPermissionModal.saveLoading = true;
                        t.$util.ajax.post('/admin/permissions', t.addEditPermissionForm).then(function (response) {
                            let response_data = response.data;
                            t.$Notice.success({
                                title: response_data.message
                            });
                            t.getList();
                            t.addEditPermissionModal.saveLoading = false;
                            t.addEditPermissionModal.show = false;
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.addEditPermissionModal.saveLoading = false;
                        })
                    }
                });
            },
            cancelEditPass() {
                let t = this;
                t.addEditPermissionModal.show = false;
                t.addEditPermissionModal.saveLoading = false;
                t.cleanModal();
            },
            addBtn() {
                this.cleanModal();
                this.addEditPermissionModal.show = true;
            },
            getList() {
                let t = this;
                t.loading = true;
                t.$util.ajax.get('/admin/permissions?search_data=' + JSON.stringify(t.search)).then(function (response) {
                    let response_data = response.data;
                    t.dataList = response_data.data;
                    t.loading = false;
                }, function (error) {
                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                    t.loading = false;
                })
            },
            deleteBtn(permission, key) {
                let t = this;
                t.$util.ajax.delete('/admin/permissions/' + permission).then(function (response) {
                    let response_data = response.data;
                    t.dataList.splice(key, 1);
                    t.$Notice.success({
                        title: response_data.message
                    });
                }, function (error) {

                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                });
            },
        },
    }
</script>
