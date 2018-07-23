<template>
    <div id="news_system-advertisement_positions-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入昵称..." v-model="search.user_name"/>
            </Col>
            <Col span="2">
                <Select v-model="search.type" placeholder="日志类型">
                    <Option value="" key="">全部类型</Option>
                    <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="3">
                <Select v-model="search.table_name" placeholder="表" filterable>
                    <Option value="" key="">全部表</Option>
                    <Option v-for="(item,key) in tableStatus.table_name" :value="key" :key="key">{{ item }}
                    </Option>
                </Select>
            </Col>
            <Col span="2">
                <Button type="primary" icon="ios-search" @click="getList(1)">Search</Button>
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
    </div>

</template>


<script>
    import expandRow from './list-table-expand';

    export default {
        components: {expandRow},
        data() {
            return {
                loading: false,
                search: {},
                tableStatus: {
                    type: [],
                    table_name: [],
                },
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
                        title: '操作人',
                        key: 'id',
                        render: (h, params) => {
                            return h('div', {
                                    class: 'green-color',
                                },
                                params.row.user.name + '--' + params.row.user.email
                            )
                        }
                    },
                    {
                        title: '类型',
                        render: (h, params) => {
                            return h('div',
                                this.tableStatus.type[params.row.type]
                            )
                        }
                    },
                    {
                        title: '表',
                        render: (h, params) => {
                            return h('div',
                                this.tableStatus.table_name[params.row.table_name]
                            )
                        }
                    },
                    {
                        title: 'IP',
                        key: 'ip',
                        width: 150
                    },
                    {
                        title: '创建时间',
                        sortable: true,
                        key: 'created_at'
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
                t.$util.ajax.get('/common_get_table_status/logs').then(function (response) {
                    let response_data = response.data;
                    t.tableStatus.type = response_data.data.type;
                    t.tableStatus.table_name = response_data.data.table_name;
                }, function (error) {

                })
            },
            handleOnPageChange: function (to_page) {
                this.getList(to_page)
            },
            getList(to_page) {
                let t = this;
                t.loading = true;
                t.feeds.current_page = to_page;
                t.$util.ajax.get('/admin/logs?page=' + to_page + '&per_page=' + t.feeds.per_page + '&search_data=' + JSON.stringify(t.search)).then(function (response) {
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
        },
    }
</script>
