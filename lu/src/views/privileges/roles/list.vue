<template>
    <div id="privileges-users-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入角色名称..." v-model="search.name"/>
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

        <Modal v-model="addEditRoleModal.show" :closable='false' :mask-closable=false :width="500">
            <h3 slot="header" style="color:#2D8CF0">编辑角色</h3>
            <Form ref="addEditRoleForm"
                  :model="addEditRoleForm" :label-width="100" label-position="right"
                  :rules="ruleAddEditRole">
                <FormItem label="角色名称" prop="name">
                    <Input v-model="addEditRoleForm.name" placeholder="请输角色名称"></Input>
                </FormItem>
                <FormItem label="看守器" prop="guard_name">
                    <Input v-model="addEditRoleForm.guard_name"
                           placeholder="请输入看守器"></Input>
                </FormItem>
                    <FormItem label="角色描述" prop="description">
                        <Input v-model="addEditRoleForm.description"
                               placeholder="请输入角色描述"></Input>
                </FormItem>

            </Form>
            <div slot="footer">
                <Button type="text" @click="cancelEditPass">取消</Button>
                <Button type="primary" @click="handleSubmit" :loading="addEditRoleModal.saveLoading">保存</Button>
            </div>
        </Modal>
        <Modal v-model="permissionModal.show" :closable='false' :mask-closable=false width="1000" >
            <h3 slot="header" style="color:#2D8CF0">分配权限</h3>
            <Transfer v-if="permissionModal.show"
                      :data="permissionModal.allPermissions"
                      :target-keys="permissionModal.hasPermissions"
                      :render-format="renderFormat"
                      :operations="['移除权限','添加权限']"
                      :list-style="permissionModal.listStyle"
                      filterable
                      @on-change="handleTransferChange">
            </Transfer>
            <div slot="footer">
                <Button type="text" @click="cancelPermissionModal">取消</Button>
                <Button type="primary" @click="handleSubmitPermission">保存
                </Button>
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
                addEditRoleModal: {
                    show: false,
                    saveLoading: false,
                },
                permissionModal: {
                    id: 0,
                    allPermissions: [],
                    hasPermissions: [],
                    show: false,
                    saveLoading: false,
                    listStyle: {
                        width:'400px',
                        height:'400px'
                    }
                },
                ruleAddEditRole: {
                    name: [
                        {required: true, message: '请填写角色限名称', trigger: 'blur'}
                    ],
                    guard_name: [
                        {required: true, message: '请填写看守器', trigger: 'blur'}
                    ],
                },
                addEditRoleForm: {
                    id: 0,
                    name: '',
                    guard_name: '',
                    description:''
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
                        title: '角色限名称',
                        key: 'name'
                    },
                    {
                        title: '角色看守器',
                        key: 'guard_name'
                    },
                    {
                        title: '角色描述',
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
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            t.addEditRoleForm = t.dataList[params.index];
                                            t.addEditRoleModal.show = true;
                                        }
                                    }

                                }, 'Edit'),
                                h('Button', {
                                    props: {
                                        type: 'info',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            t.getRolePermissions(params.row.id);
                                            t.permissionModal.show = true;
                                            t.permissionModal.id = params.row.id;
                                        }
                                    }

                                }, '权限'),

                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除「'+params.row.name+'」角色？',
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
                            ]);
                        },
                    },
                ]
            }
        },

        created() {
            let t = this;
            t.getList();
            t.getAllPermission();
        },
        methods: {
            renderFormat(item) {
                return item.label + '「'+item.description+'」'
            },
            handleSubmitPermission() {
                let t = this;
                t.$util.ajax.post('/admin/give/' + t.permissionModal.id + '/permissions', {
                    permission: t.permissionModal.hasPermissions
                }).then(function (response) {
                    let response_data = response.data;

                    t.$Notice.success({
                        title: response_data.message
                    });
                    t.permissionModal.show = false;
                }, function (error) {

                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                });
            },
            cancelPermissionModal() {
                let t = this;
                t.permissionModal.show = false;
                t.permissionModal.saveLoading = false;
            },
            getAllPermission() {
                let t = this;
                t.$util.ajax.get('/admin/all_permissions').then(function (response) {
                    let response_data = response.data;
                    t.permissionModal.allPermissions = response_data.data;
                }, function (error) {
                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                })
            },
            handleTransferChange(newTargetKeys) {
                this.permissionModal.hasPermissions = newTargetKeys;
            },
            getRolePermissions(id) {
                let t = this;
                t.$util.ajax.get('/admin/roles/' + id + '/permissions').then(function (response) {
                    let response_data = response.data;
                    t.permissionModal.hasPermissions = response_data.data;
                }, function (error) {
                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                })
            },
            handleSubmit() {
                let t = this;
                t.$refs.addEditRoleForm.validate((valid) => {
                    if (valid) {
                        t.addEditRoleModal.saveLoading = true;
                        t.$util.ajax.post('/admin/roles', t.addEditRoleForm).then(function (response) {
                            let response_data = response.data;
                            t.$Notice.success({
                                title: response_data.message
                            });
                            t.getList();
                            t.addEditRoleModal.saveLoading = false;
                            t.addEditRoleModal.show = false;
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.addEditRoleModal.saveLoading = false;
                        })
                    }
                });
            },
            cancelEditPass() {
                let t = this;
                t.addEditRoleModal.show = false;
                t.addEditRoleModal.saveLoading = false;
                t.cleanModal();
            },
            cleanModal() {
                let t = this;
                t.addEditRoleForm = {
                    id: 0,
                    name: '',
                    guard_name: '',
                    description:''
                };
            },
            addBtn() {
                this.cleanModal();
                this.addEditRoleModal.show = true;
            },
            getList() {
                let t = this;
                t.loading = true;
                t.$util.ajax.get('/admin/roles?search_data=' + JSON.stringify(t.search)).then(function (response) {
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
            deleteBtn(role, key) {
                let t = this;
                t.$util.ajax.delete('/admin/roles/' + role).then(function (response) {
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
