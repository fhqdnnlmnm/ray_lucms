<template>
    <div id="privileges-users-list">
        <Row type="flex" justify="end" class="code-row-bg" :gutter="16">
            <Col span="2">
                <Select v-model="search.enable" placeholder="请选择状态">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="2">
                <Select v-model="search.is_admin" placeholder="管理员">
                    <Option value="" key="">全部</Option>
                    <Option v-for="(item,key) in tableStatus.is_admin" :value="key" :key="key">{{ item }}</Option>
                </Select>
            </Col>
            <Col span="3">
                <Input icon="search" placeholder="请输入邮箱搜索..." v-model="search.email"/>
            </Col>
            <Col span="2">
                <Button type="primary" icon="ios-search" @click="getList(feeds.current_page)">Search</Button>
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
        <Modal v-model="modalHeadImage.show">
            <p slot="header">图片预览</p>
            <div class="text-center">
                <img :src="modalHeadImage.url" alt="" v-if="modalHeadImage.show" class="center-align">
            </div>
            <div slot="footer">
            </div>
        </Modal>
        <Modal v-model="roleModal.show" :closable='false' :mask-closable=false width="1000">
            <h3 slot="header" style="color:#2D8CF0">分配权限</h3>
            <Transfer v-if="roleModal.show"
                      :data="roleModal.allRoles"
                      :target-keys="roleModal.hasRoles"
                      :render-format="renderFormat"
                      :operations="['移除角色','添加角色']"
                      :list-style="roleModal.listStyle"
                      filterable
                      @on-change="handleTransferChange">
            </Transfer>
            <div slot="footer">
                <Button type="text" @click="cancelRoleModal">取消</Button>
                <Button type="primary" @click="handleSubmitRole">保存
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
                tableStatus: {
                    enable: [],
                    is_admin: [],
                },
                search: {},
                roleModal: {
                    id: 0,
                    allRoles: [],
                    hasRoles: [],
                    show: false,
                    saveLoading: false,
                    listStyle: {
                        width: '400px',
                        height: '400px'
                    }
                },
                feeds: {
                    data: [],
                    total: 0,
                    current_page: 1,
                    per_page: 10
                },
                columns: [
                    {
                        title: 'ID',
                        key: 'id',
                        sortable: true,
                        width: 100
                    },
                    {
                        title: '昵称',
                        key: 'name'
                    },
                    {
                        title: '头像',
                        key: '',
                        render: (h, params) => {
                            let t = this;
                            return h('div', [
                                h('img', {
                                    attrs: {
                                        src: params.row.head_image.url,
                                        'data-fancybox': '12312'
                                    },
                                    class: 'head_image',
                                    style: {
                                        width: '40px',
                                        height: '40px'
                                    },
                                    on: {
                                        click: (value) => {
                                            t.modalHeadImage.show = true;
                                            t.modalHeadImage.url = params.row.head_image.url;
                                        }
                                    }
                                }),
                            ]);
                        }
                    },
                    {
                        title: '邮箱',
                        key: 'email'
                    },
                    {
                        title: '后台权限',
                        width: 150,
                        render: (h, params) => {

                            const row = params.row;
                            const color = row.is_admin === 'T' ? 'green' : 'red';
                            const text = row.is_admin === 'T' ? '可登录' : '不可登录';

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
                        key: 'created_at',
                        sortable: true
                    },
                    {
                        title: '最近登录时间',
                        key: 'last_login_at',
                        sortable: true
                    },
                    {
                        title: '操作',
                        key: '',
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
                                            let argu = {user_id: params.row.id};
                                            this.$router.push({
                                                name: 'edit-user',
                                                params: argu
                                            });
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
                                            t.getUserRoles(params.row.id);
                                            t.roleModal.show = true;
                                            t.roleModal.id = params.row.id;
                                        }
                                    }

                                }, '角色'),

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
                    }
                ],

            }
        },
        created() {
            let t = this;
            t.getList(t.feeds.current_page);
            t.getAllRole();
            t.getTableStatus();
        },
        methods: {
            handleOnPageChange: function (to_page) {
                this.getList(to_page)
            },
            getTableStatus() {
                let t = this;
                t.$util.ajax.get('/common_get_table_status/users').then(function (response) {
                    let response_data = response.data;
                    t.tableStatus.enable = response_data.data.enable;
                    t.tableStatus.is_admin = response_data.data.is_admin;
                }, function (error) {

                })
            },
            getList(to_page) {
                let t = this;
                t.loading = true;
                t.feeds.current_page = to_page;
                t.$util.ajax.get('/admin/users?page=' + to_page + '&per_page=' + t.feeds.per_page + '&search_data=' + JSON.stringify(t.search)).then(function (response) {
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
                    table: 'users',
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
            getAllRole() {
                let t = this;
                t.$util.ajax.get('/admin/all_roles').then(function (response) {
                    let response_data = response.data;
                    t.roleModal.allRoles = response_data.data;
                }, function (error) {
                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                })
            },
            handleTransferChange(newTargetKeys) {
                this.roleModal.hasRoles = newTargetKeys;
            },
            getUserRoles(id) {
                let t = this;
                t.$util.ajax.get('/admin/users/' + id + '/roles').then(function (response) {
                    let response_data = response.data;
                    t.roleModal.hasRoles = response_data.data;
                }, function (error) {
                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                })
            },
            renderFormat(item) {
                return item.label + '「' + item.description + '」'
            },
            handleSubmitRole() {
                let t = this;
                t.$util.ajax.post('/admin/give/' + t.roleModal.id + '/roles', {
                    role: t.roleModal.hasRoles
                }).then(function (response) {
                    let response_data = response.data;

                    t.$Notice.success({
                        title: response_data.message
                    });
                    t.roleModal.show = false;
                }, function (error) {

                    t.$Notice.warning({
                        title: '出错了',
                        desc: error.message
                    });
                });
            },
            cancelRoleModal() {
                let t = this;
                t.roleModal.show = false;
                t.roleModal.saveLoading = false;
            },
            deleteBtn(user, key) {
                let t = this;
                t.$util.ajax.delete('/admin/users/' + user).then(function (response) {
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
            addBtn() {
                this.$router.push({
                    name: 'add-user',
                });
            },
        },
    }
</script>

