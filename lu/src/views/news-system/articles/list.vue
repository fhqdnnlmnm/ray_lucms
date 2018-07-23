<template>
    <div id="news_system-advertisement_positions-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="3">
                <Input icon="search" placeholder="请输入标题..." v-model="search.title"/>
            </Col>
            <Col span="2">
                <Select v-model="search.enable" placeholder="是否启用">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Select v-model="search.category_id" placeholder="分类" filterable>
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in article_categories" :value="item.id" :key="item.id">{{ item.name }}
                    </Option>
                </Select>
            </Col>
            <Col span="2">
                <Select v-model="search.recommend" placeholder="推荐" filterable>
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.recommend" :value="key" :key="key">{{ item }}
                    </Option>
                </Select>
            </Col>
            <Col span="2">
                <Select v-model="search.top" placeholder="置顶" filterable>
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.top" :value="key" :key="key">{{ item }}
                    </Option>
                </Select>
            </Col>
            <Col span="2">
                <Button type="primary" icon="ios-search" @click="getList(1)">Search</Button>
            </Col>
            <Col span="2" v-if="canAccess">
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
                article_categories: {},
                tableStatus: {
                    enable: [],
                    recommend: [],
                    top: [],
                    access_type: [],
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
                        title: '标题',
                        key: 'title',
                        width: 150,
                    },
                    {
                        title: '封面',
                        width: 150,
                        render: (h, params) => {
                            let t = this;
                            if (params.row.cover_image.url) {
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
                        }
                    },
                    {
                        title: '分类',
                        width: 100,
                        render: (h, params) => {
                            return h('div',
                                params.row.category.name
                            )
                        }
                    },
                    {
                        title: '标签',
                        width: 200,
                        render: (h, params) => {
                            var tags = params.row.tags;
                            var text = '';
                            for (var key in tags) {
                                if (key < 1) {
                                    text += tags[key].name
                                } else {
                                    text += '、' + tags[key].name
                                }
                            }
                            return h('div',
                                text
                            )
                        }
                    },
                    {
                        title: '置顶',
                        width: 150,
                        render: (h, params) => {
                            var row = params.row;
                            var color = 'green';
                            var text = '置顶';
                            if (row.top === 'F') {
                                text = '非置顶';
                                color = 'default';
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
                        title: '推荐',
                        width: 150,
                        render: (h, params) => {
                            var row = params.row;
                            var color = 'green';
                            var text = '推荐';
                            if (row.recommend === 'F') {
                                text = '非推荐';
                                color = 'default';
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
                        title: '创建时间',
                        sortable: true,
                        key: 'created_at'
                    },
                    {
                        title: '操作',
                        render: (h, params) => {
                            let t = this;
                            let canAccess = t.canAccess;
                            var delete_btn = '';

                            if( canAccess) {
                                 delete_btn = h('Poptip', {
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
                            }
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'success',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => {
                                            this.$router.push({
                                                name: 'edit-article',
                                                params: {article_id: params.row.id}
                                            });
                                        }
                                    }

                                }, 'Edit'),
                                delete_btn
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
            t.getArticleCategories();
        },
        computed: {
            canAccess() {
                return true;
                // return this.$util.showThisRoute(['Founder', 'news_listor'], JSON.parse(this.$cookie.get('current_roles')))
            }
        },
        methods: {
            getTableStatus() {
                let t = this;
                t.$util.ajax.get('/common_get_table_status/articles').then(function (response) {
                    let response_data = response.data;
                    t.tableStatus.enable = response_data.data.enable;
                    t.tableStatus.recommend = response_data.data.recommend;
                    t.tableStatus.top = response_data.data.top;
                }, function (error) {

                })
            },
            getArticleCategories() {
                let t = this;
                t.$util.ajax.get('/admin/categories/all').then(function (response) {
                    let response_data = response.data;
                    t.article_categories = response_data.data;
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
                t.$util.ajax.get('/admin/articles?page=' + to_page + '&per_page=' + t.feeds.per_page + '&search_data=' + JSON.stringify(t.search)).then(function (response) {
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
                    name: 'add-article',
                });
            },
            deleteBtn(article, key) {
                let t = this;
                t.$util.ajax.delete('/admin/articles/' + article).then(function (response) {
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
                    table: 'articles',
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
