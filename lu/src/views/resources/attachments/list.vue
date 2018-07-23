<template>
    <div id="resources-attachments-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="2">
                <Select v-model="search.use_status" placeholder="请选择使用状态">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.use_status" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Select v-model="search.enable" placeholder="请选择状态">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Select v-model="search.type" placeholder="请选择附件类型">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Select v-model="search.storage_position" placeholder="请选择存储位置">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.storage_position" :value="key" :key="key">{{ item }}
                    </Option>
                </Select>
            </Col>
            <Col span="2">
                <Button type="primary" icon="ios-search" @click="getList(feeds.current_page)">Search</Button>
            </Col>
        </Row>
        <br/>
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
    </div>

</template>


<script>
    import expandRow from './list-table-expand';

    export default {
        components: {expandRow},
        data() {
            return {
                tableStatus: {
                    enable: [],
                    use_status: [],
                    type: [],
                    storage_position: [],
                },
                loading: false,
                search: {},
                feeds: {
                    data: [],
                    total: 0,
                    current_page: 1,
                    per_page: 10
                },
                columns: [
                    {
                        title: '>>',
                        type: 'expand',
                        width: 50,
                        render: (h, params) => {
                            return h(expandRow, {
                                props: {
                                    row: params.row
                                }
                            })
                        }
                    },
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: true,
                        width: 100
                    },
                    {
                        title: '附件名称',
                        key: 'original_name'
                    },
                    {
                        title: '上传者',
                        width: 200,
                        render: (h, params) => {
                            return h('div',
                                params.row.user.name
                            );
                        }
                    },
                    {
                        title: '使用状态',
                        width: 150,
                        render: (h, params) => {

                            const row = params.row;
                            const color = row.use_status === 'T' ? 'green' : 'default';
                            const text = row.use_status === 'T' ? '使用中' : '未使用';

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
                        title: '附件类型',
                        key: 'type',
                        width: 100
                    },
                    {
                        title: 'MIME 类型',
                        key: 'mime_type',
                        width: 100
                    },
                    {
                        title: '存储位置',
                        key: 'storage_position',
                        width: 100
                    },
                    {
                        title: '大小/kb',
                        key: 'size',
                        sortable: true,
                        width: 100
                    },
                    {
                        title: '启用状态',
                        key: 'enable',
                        render: (h, params) => {
                            return h('i-switch', {
                                props: {
                                    slot: 'open',
                                    type: 'primary',
                                    value: params.row.enable === 'T',
                                },
                                on: {
                                    'on-change': (value) => {
                                        this.switchEnable(params.index)
                                    }
                                }
                            });
                        }
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        sortable: true,
                    },
                    {
                        title: '操作',

                        render: (h, params) => {
                            let t = this;
                            return h('div', [
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除「' + params.row.original_name + '」？',
                                        transfer: true
                                    },
                                    on: {
                                        'on-ok': () => {
                                            if (params.row.enable === 'T') {
                                                t.$Notice.warning({
                                                    title: '出错了',
                                                    desc: '启用状态的附件无法删除'
                                                });
                                            } else {
                                                t.deleteBtn(params.row.id, params.index);
                                            }
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
            handleOnPageChange: function (to_page) {
                this.getList(to_page)
            },
            getTableStatus() {
                let t = this;
                t.$util.ajax.get('/common_get_table_status/attachments').then(function (response) {
                    let response_data = response.data;
                    t.tableStatus.enable = response_data.data.enable;
                    t.tableStatus.use_status = response_data.data.use_status;
                    t.tableStatus.type = response_data.data.type;
                    t.tableStatus.storage_position = response_data.data.storage_position;
                }, function (error) {

                })
            },
            getList(to_page) {
                let t = this;
                t.loading = true;
                t.feeds.current_page = to_page;
                t.$util.ajax.get('/admin/attachments?page=' + to_page + '&per_page=' + t.feeds.per_page + '&search_data=' + JSON.stringify(t.search)).then(function (response) {
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
            switchEnable(index) {
                let t = this;
                let new_status = 'T';
                if (t.feeds.data[index].enable === 'T') {
                    new_status = 'F';
                }
                t.$util.ajax.post('/common_switch_enable', {
                    table: 'attachments',
                    id: t.feeds.data[index].id,
                    value: new_status
                }).then(function (response) {
                    let response_data = response.data;
                    t.feeds.data[index].enable = new_status;
                    t.$Notice.success({
                        title: response_data.message
                    })
                }, function (error) {
                    t.feeds.data[index].enable = new_status;
                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                })
            },
            deleteBtn(attachment, key) {
                let t = this;
                t.$util.ajax.delete('/admin/attachments/' + attachment).then(function (response) {
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

        }

    }
</script>
