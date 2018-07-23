<template>
    <div id="news_system-advertisement_positions-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入广告位名称..." v-model="search.name"/>
            </Col>
            <Col span="3">
                <Select v-model="search.type" placeholder="请选择广告位类型">
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

        <Modal v-model="addEditAdvertisementPositionModal.show" :closable='false' :mask-closable=false :width="500">
            <h3 slot="header" style="color:#2D8CF0">编辑广告位</h3>
            <Form ref="addEditAdvertisementPositionForm"
                  :model="addEditAdvertisementPositionForm" :label-width="100" label-position="right"
                  :rules="ruleAddEditAdvertisementPosition">
                <FormItem label="广告位名称" prop="name">
                    <Input v-model="addEditAdvertisementPositionForm.name" placeholder="请输入广告位名称"></Input>
                </FormItem>
                <FormItem label="广告位类型" prop="type">
                    <Select v-model="addEditAdvertisementPositionForm.type" filterable>
                        <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
                    </Select>
                </FormItem>
                <FormItem label="广告位描述" prop="description">
                    <Input type="textarea" v-model="addEditAdvertisementPositionForm.description"
                           placeholder="请输入描述"></Input>
                </FormItem>

            </Form>
            <div slot="footer">
                <Button type="text" @click="cancelEditPass">取消</Button>
                <Button type="primary" @click="handleSubmit" :loading="addEditAdvertisementPositionModal.saveLoading">
                    保存
                </Button>
            </div>
        </Modal>
    </div>

</template>


<script>
    export default {
        data() {
            return {
                loading: false,
                search: {},
                tableStatus: {
                    type: [],
                },
                feeds: {
                    data: [],
                    total: 0,
                    current_page: 1,
                    per_page: 10
                },
                addEditAdvertisementPositionModal: {
                    show: false,
                    saveLoading: false,
                },
                ruleAddEditAdvertisementPosition: {
                    name: [
                        {required: true, message: '请填写广告位名称', trigger: 'blur'}
                    ],
                    type: [
                        {required: true, message: '请选择类型', trigger: 'blur'}
                    ],
                },
                addEditAdvertisementPositionForm: {
                    id: 0,
                    name: '',
                    type: '',
                    description: '',
                },
                columns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: true,
                        width: 100
                    },
                    {
                        title: '广告位名称',
                        key: 'name'
                    },
                    {
                        title: '广告位描述',
                        key: 'description'
                    },
                    {
                        title: '类型',
                        render: (h, params) => {
                            return h('div', [
                                h('Tag', {
                                    props: {
                                        type: 'dot',
                                        color: 'green'
                                    }
                                }, this.tableStatus.type[params.row.type])
                            ]);
                        },
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
                                            t.addEditAdvertisementPositionForm = t.feeds.data[params.index];
                                            t.addEditAdvertisementPositionModal.show = true;
                                        }
                                    }

                                }, 'Edit'),
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除「' + params.row.name + '」广告位？',
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
            t.getList(t.feeds.current_page);
            t.getTableStatus();
        },
        methods: {
            getTableStatus() {
                let t = this;
                t.$util.ajax.get('/common_get_table_status/advertisement_positions/type').then(function (response) {
                    let response_data = response.data;
                    t.tableStatus.type = response_data.data;
                }, function (error) {

                })
            },
            handleOnPageChange: function (to_page) {
                this.getList(to_page)
            },
            cleanModal() {
                let t = this;
                t.addEditAdvertisementPositionForm = {
                    id: 0,
                    name: '',
                    type: '',
                    description: ''
                };
            },
            handleSubmit() {
                let t = this;
                t.$refs.addEditAdvertisementPositionForm.validate((valid) => {
                    if (valid) {
                        t.addEditAdvertisementPositionModal.saveLoading = true;
                        t.$util.ajax.post('/admin/advertisement_positions', t.addEditAdvertisementPositionForm).then(function (response) {
                            let response_data = response.data;
                            t.$Notice.success({
                                title: response_data.message
                            });
                            t.getList(1);
                            t.addEditAdvertisementPositionModal.saveLoading = false;
                            t.addEditAdvertisementPositionModal.show = false;
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.addEditAdvertisementPositionModal.saveLoading = false;
                        })
                    }
                });
            },
            cancelEditPass() {
                let t = this;
                t.addEditAdvertisementPositionModal.show = false;
                t.addEditAdvertisementPositionModal.saveLoading = false;
                t.cleanModal();
            },
            addBtn() {
                this.cleanModal();
                this.addEditAdvertisementPositionModal.show = true;
            },
            getList(to_page) {
                let t = this;
                t.loading = true;
                t.feeds.current_page = to_page;
                t.$util.ajax.get('/admin/advertisement_positions?page=' + to_page + '&per_page=' + t.feeds.per_page + '&search_data=' + JSON.stringify(t.search)).then(function (response) {
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
            deleteBtn(advertisement_position, key) {
                let t = this;
                t.$util.ajax.delete('/admin/advertisement_positions/' + advertisement_position).then(function (response) {
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
