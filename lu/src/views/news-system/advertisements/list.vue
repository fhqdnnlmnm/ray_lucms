<template>
    <div id="news_system-advertisement_positions-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入广告标题..." v-model="search.name"/>
            </Col>
            <Col span="2">
                <Select v-model="search.enable" placeholder="是否启用">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="3">
                <Select v-model="search.advertisement_positions_id" filterable placeholder="请选择广告位类型">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in advertisement_positions_ids" :value="item.id" :key="item.id">{{
                        item.name }}
                    </Option>
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
        <Modal v-model="modalHeadImage.show" width="1000px">
            <p slot="header">图片预览</p>
            <div class="text-center">
                <img :src="modalHeadImage.url" alt="" v-if="modalHeadImage.show" class="center-align">
            </div>
            <div slot="footer">
            </div>
        </Modal>

    </div>

</template>


<script>
    import expandRow from './list-table-expand';

    export default {
        components: {expandRow},
        data() {
            return {
                modalHeadImage: {
                    show: false,
                    url: null
                },
                loading: false,
                search: {},
                advertisement_positions_ids: {},
                tableStatus: {
                    enable: [],
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
                        title: '广告标题',
                        key: 'name'
                    },
                    {
                        title: '封面',
                        width: 200,
                        render: (h, params) => {
                            let t = this;
                            return h('div', [
                                h('img', {
                                    attrs: {
                                        src: params.row.cover_image.url,
                                    },
                                    style: {
                                        width: '40px',
                                        height: '40px'
                                    },
                                    on: {
                                        click: (value) => {
                                            t.modalHeadImage.show = true;
                                            t.modalHeadImage.url = params.row.cover_image.url;
                                        }
                                    }
                                }),
                            ]);
                        }
                    },
                    {
                        title: '广告位',
                        width: 150,
                        render: (h, params) => {
                            return h('div',
                                params.row.advertisement_position.name
                            )
                        }
                    },
                    {
                        title: '启用状态',
                        key: 'enable',
                        render: (h, params) => {
                            return h('i-switch', {
                                props: {
                                    slot: 'open',
                                    type: 'primary',
                                    value: params.row.enable === 'T',  //控制开关的打开或关闭状态，官网文档属性是value
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
                        title: '有效期',
                        width:350,
                        render: (h, params) => {

                            const row = params.row;
                            var color = 'green';
                            var text = row.start_at ? row.start_at : '永久有效';
                                text += row.end_at ? '--' + row.end_at : '';
                            if (row.overdue_time < 24 * 3600 && (row.overdue_time > 0 )) {
                                color = 'yellow';
                            } else if (row.overdue_time < 0 ) {
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
                        title: '创建时间',
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
                                            this.$router.push({
                                                name: 'edit-advertisement',
                                                params: {advertisement_id: params.row.id}
                                            });
                                        }
                                    }

                                }, 'Edit'),
                                h('Poptip', {
                                    props: {
                                        confirm: true,
                                        title: '您确定要删除「' + params.row.name + '」广告？',
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
            t.getAdvertisementPositions();
        },
        methods: {
            getTableStatus() {
                let t = this;
                t.$util.ajax.get('/common_get_table_status/advertisements/enable').then(function (response) {
                    let response_data = response.data;
                    t.tableStatus.enable = response_data.data;
                }, function (error) {

                })
            },
            getAdvertisementPositions() {
                let t = this;
                t.$util.ajax.get('/admin/advertisement_positions/all').then(function (response) {
                    let response_data = response.data;
                    t.advertisement_positions_ids = response_data.data;
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
                t.$util.ajax.get('/admin/advertisements?page=' + to_page + '&per_page=' + t.feeds.per_page + '&search_data=' + JSON.stringify(t.search)).then(function (response) {
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
            addBtn() {
                this.$router.push({
                    name: 'add-advertisement',
                });
            },
            deleteBtn(advertisement, key) {
                let t = this;
                t.$util.ajax.delete('/admin/advertisements/' + advertisement).then(function (response) {
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
            switchEnable(index) {
                let t = this;
                let new_status = 'T';
                if (t.feeds.data[index].enable === 'T') {
                    new_status = 'F';
                }
                t.$util.ajax.post('/common_switch_enable', {
                    table: 'advertisements',
                    id: t.feeds.data[index].id,
                    value: new_status
                }).then(function (response) {
                    t.feeds.data[index].enable = new_status;
                    let response_data = response.data;
                    t.$Notice.success({
                        title: response_data.message
                    })
                }, function (error) {
                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                })
            },
        },
    }
</script>
