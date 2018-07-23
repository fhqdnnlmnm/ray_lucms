<template>
    <div id="privileges-users-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入名称..." v-model="search.name"/>
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

        <Modal v-model="addEditTagModal.show" :closable='false' :mask-closable=false :width="800">
            <h3 slot="header" style="color:#2D8CF0">编辑</h3>
            <Form ref="addEditTagForm"
                  :model="addEditTagForm" :label-width="100" label-position="right"
                  :rules="ruleAddEditTag">
                <FormItem label="名称" prop="name">
                    <Input v-model="addEditTagForm.name" placeholder="请输入名称"></Input>
                </FormItem>

            </Form>
            <div slot="footer">
                <Button type="text" @click="cancelEditPass">取消</Button>
                <Button type="primary" @click="handleSubmit" :loading="addEditTagModal.saveLoading">保存</Button>
            </div>
        </Modal>
    </div>

</template>


<script>
    export default {
        data() {
            return {
                loading: false,
                search: {
                    name: null,
                },
                addEditTagModal: {
                    show: false,
                    saveLoading: false,
                },
                ruleAddEditTag: {
                    name: [
                        {required: true, message: '请填写名称', trigger: 'blur'}
                    ],
                },
                addEditTagForm: {
                    id: 0,
                    name: '',
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
                        title: '名称',
                        key: 'name'
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
                                            t.addEditTagForm = t.dataList[params.index];
                                            t.addEditTagModal.show = true;
                                        }
                                    }

                                }, 'Edit'),
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除「' + params.row.name + '」？',
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
                t.addEditTagForm = {
                    id: 0,
                    name: '',
                };
            },
            handleSubmit() {
                let t = this;
                t.$refs.addEditTagForm.validate((valid) => {
                    if (valid) {
                        t.addEditTagModal.saveLoading = true;
                        t.$util.ajax.post('/admin/tags', t.addEditTagForm).then(function (response) {
                            let response_data = response.data;
                            t.$Notice.success({
                                title: response_data.message
                            });
                            t.getList();
                            t.addEditTagModal.saveLoading = false;
                            t.addEditTagModal.show = false;
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.addEditTagModal.saveLoading = false;
                        })
                    }
                });
            },
            cancelEditPass() {
                let t = this;
                t.addEditTagModal.show = false;
                t.addEditTagModal.saveLoading = false;
                t.cleanModal();
            },
            addBtn() {
                this.cleanModal();
                this.addEditTagModal.show = true;
            },
            getList() {
                let t = this;
                t.loading = true;
                t.$util.ajax.get('/admin/tags?search_data=' + JSON.stringify(t.search)).then(function (response) {
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
            deleteBtn(category, key) {
                let t = this;
                t.$util.ajax.delete('/admin/tags/' + category).then(function (response) {
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
