<template>
    <div id="privileges-users-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入ip..." v-model="search.ip"/>
            </Col>
            <Col span="2">
                <Select v-model="search.type" placeholder="类型">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Button type="primary" icon="ios-search" @click="getList(1)">Search</Button>
            </Col>
            <Col span="2">
                <Button type="success" icon="plus" @click="addBtn()">Add</Button>
            </Col>
        </Row>
        <br>

        <Row>
            <Table border :columns="columns" :data="feeds.data" :loading="loading"></Table>
            <div style="margin: 10px;overflow: hidden">
                <div style="float: right;">
                    <Page :total="feeds.total" :current="feeds.current_page" :page-size="feeds.per_page" class="paging"
                          show-elevator show-total
                          @on-change="handleOnPageChange"></Page>
                </div>
            </div>
        </Row>

        <Modal v-model="addEditIpFilterModal.show" :closable='false' :mask-closable=false :width="600">
            <h3 slot="header" style="color:#2D8CF0">编辑</h3>
            <Form ref="addEditIpFilterForm"
                  :model="addEditIpFilterForm" :label-width="100" label-position="right"
                  :rules="ruleAddEditIpFilter">
                <FormItem label="类型" prop="type">
                    <Select v-model="addEditIpFilterForm.type" filterable placeholder="请选择类型" style="width:60%">
                        <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
                    </Select>
                </FormItem>
                <FormItem label="ip" prop="ip">
                    <Input v-model="addEditIpFilterForm.ip" placeholder="请输入ip" style="width: 60%"></Input>
                </FormItem>

            </Form>
            <div slot="footer">
                <Button type="text" @click="cancelEditPass">取消</Button>
                <Button type="primary" @click="handleSubmit" :loading="addEditIpFilterModal.saveLoading">保存</Button>
            </div>
        </Modal>
    </div>

</template>


<script>
    export default {
        data() {
            return {
                loading: false,
                tableStatus: {
                    type: [],
                },
                search: {},
                feeds: {
                    data: [],
                    total: 0,
                    current_page: 1,
                    per_page: 10
                },
                addEditIpFilterModal: {
                    show: false,
                    saveLoading: false,
                },
                ruleAddEditIpFilter: {
                    ip: [
                        {required: true, message: '请填写ip', trigger: 'blur'}
                    ],
                },
                addEditIpFilterForm: {
                    id: 0,
                    type: '',
                    ip: '',
                },
                columns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: true,
                        width: 100
                    },
                    {
                        title: '类型',
                        render: (h, params) => {
                            var row = params.row;
                            var color = 'green';
                            var text = '白名单';
                            if (row.type === 'black') {
                                text = '黑名单';
                                color = 'red';
                            }

                            return h('div', [
                                h('Tag', {
                                    props: {
                                        type: 'dot',
                                        color: color
                                    }
                                }, text)
                            ]);
                        }
                    },
                    {
                        title: 'ip',
                        key: 'ip'
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
                                            t.addEditIpFilterForm = params.row;
                                            t.addEditIpFilterModal.show = true;
                                        }
                                    }

                                }, 'Edit'),
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除「' + params.row.ip + '」？',
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
            t.getTableStatus();
            t.getList(t.feeds.current_page);
        },
        methods: {
            getTableStatus() {
                let t = this;
                t.$util.ajax.get('/common_get_table_status/ip_filters/type').then(function (response) {
                    let response_data = response.data;
                    t.tableStatus.type = response_data.data;
                }, function (error) {

                })
            },
            cleanModal() {
                let t = this;
                t.addEditIpFilterForm = {
                    id: 0,
                    type: 'white',
                    value: '',
                };
            },
            handleSubmit() {
                let t = this;
                t.$refs.addEditIpFilterForm.validate((valid) => {
                    if (valid) {
                        t.addEditIpFilterModal.saveLoading = true;
                        t.$util.ajax.post('/admin/ip_filters', t.addEditIpFilterForm).then(function (response) {
                            let response_data = response.data;
                            t.$Notice.success({
                                title: response_data.message
                            });
                            t.getList();
                            t.addEditIpFilterModal.saveLoading = false;
                            t.addEditIpFilterModal.show = false;
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.addEditIpFilterModal.saveLoading = false;
                        })
                    }
                });
            },
            cancelEditPass() {
                let t = this;
                t.addEditIpFilterModal.show = false;
                t.addEditIpFilterModal.saveLoading = false;
                t.cleanModal();
            },
            addBtn() {
                this.cleanModal();
                this.addEditIpFilterModal.show = true;
            },
            handleOnPageChange: function (to_page) {
                this.getList(to_page)
            },
            getList(to_page) {
                let t = this;
                t.loading = true;
                t.feeds.current_page = to_page;
                t.$util.ajax.get('/admin/ip_filters?page=' + to_page + '&per_page=' + t.feeds.per_page + '&search_data=' + JSON.stringify(t.search)).then(function (response) {
                    let response_data = response.data;
                    t.feeds.data = response_data.data;
                    t.feeds.total = response_data.meta.total;
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
                t.$util.ajax.delete('/admin/ip_filters/' + category).then(function (response) {
                    let response_data = response.data;
                    t.feeds.data.splice(key, 1);
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
