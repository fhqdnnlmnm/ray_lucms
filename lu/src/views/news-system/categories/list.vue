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

        <Modal v-model="addEditCategoryModal.show" :closable='false' :mask-closable=false :width="800">
            <h3 slot="header" style="color:#2D8CF0">编辑</h3>
            <Form ref="addEditCategoryForm"
                  :model="addEditCategoryForm" :label-width="100" label-position="right"
                  :rules="ruleAddEditCategory">
                <FormItem label="名称" prop="name">
                    <Input v-model="addEditCategoryForm.name" placeholder="请输入名称"></Input>
                </FormItem>

                <FormItem label="封面：">
                    <div style="display:inline-block;width:50%">
                        <Upload
                                :show-upload-list="false"
                                :on-success="handleSuccess"
                                :on-format-error="handleFormatError"
                                :on-exceeded-size="handleMaxSize"
                                :headers="uploadConfig.headers"
                                :max-size="uploadConfig.max_size"
                                :format="uploadConfig.format"
                                :data="uploadConfig.data"
                                name="other"
                                type="drag"
                                :action="uploadConfig.uploadUrl"
                                style="display: inline-block;width:58px;"
                        >
                            <div style="width: 58px;height:58px;line-height: 58px;">
                                <Icon type="camera" size="20"></Icon>
                            </div>
                        </Upload>
                        <img :src="addEditCategoryForm.cover_image.url" alt=""
                        v-if="addEditCategoryForm.cover_image.url" class="normall-image">
                    </div>
                </FormItem>
                <FormItem label="描述" prop="description">
                    <Input type="textarea" v-model="addEditCategoryForm.description"
                           placeholder="请输入描述"></Input>
                </FormItem>


            </Form>
            <div slot="footer">
                <Button type="text" @click="cancelEditPass">取消</Button>
                <Button type="primary" @click="handleSubmit" :loading="addEditCategoryModal.saveLoading">保存</Button>
            </div>
        </Modal>
        <Modal v-model="modalCoverImage.show">
            <p slot="header">图片预览</p>
            <div class="text-center">
                <img :src="modalCoverImage.url" alt="" v-if="modalCoverImage.show" class="center-align">
            </div>
            <div slot="footer">
            </div>
        </Modal>
    </div>

</template>


<script>
    export default {
        data() {
            return {
                modalCoverImage: {
                    show: false,
                    url: null
                },
                loading: false,
                search: {
                    name: null,
                },
                addEditCategoryModal: {
                    show: false,
                    saveLoading: false,
                },
                uploadConfig: {
                    headers: {
                        'Authorization': localStorage.access_token_type + ' ' + localStorage.access_token,
                    },
                    format: ['jpg', 'jpeg', 'png', 'gif'],
                    max_size: 500,
                    uploadUrl: window.uploadUrl.uploadOther,
                    data: {
                        max_width: 800
                    }
                },
                ruleAddEditCategory: {
                    name: [
                        {required: true, message: '请填写名称', trigger: 'blur'}
                    ],
                },
                addEditCategoryForm: {
                    id: 0,
                    name: '',
                    description: '',
                    cover_image: {
                        attachment_id: 0,
                        url: '',
                    },
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
                        title: '封面',
                        key: '',
                        render: (h, params) => {
                            let t = this;
                            if(params.row.cover_image.url) {
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
                                                t.modalCoverImage.show = true;
                                                t.modalCoverImage.url = params.row.cover_image.url;
                                            }
                                        }
                                    }),
                                ]);
                            }
                        }
                    },
                    {
                        title: '描述',
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
                                            t.addEditCategoryForm = t.dataList[params.index];
                                            t.addEditCategoryModal.show = true;
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
                t.addEditCategoryForm = {
                    id: 0,
                    name: '',
                    description: '',
                    cover_image: {
                        attachment_id: 0,
                        url: '',
                    },
                };
            },
            handleSubmit() {
                let t = this;
                t.$refs.addEditCategoryForm.validate((valid) => {
                    if (valid) {
                        t.addEditCategoryModal.saveLoading = true;
                        t.$util.ajax.post('/admin/categories', t.addEditCategoryForm).then(function (response) {
                            let response_data = response.data;
                            t.$Notice.success({
                                title: response_data.message
                            });
                            t.getList();
                            t.addEditCategoryModal.saveLoading = false;
                            t.addEditCategoryModal.show = false;
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.addEditCategoryModal.saveLoading = false;
                        })
                    }
                });
            },
            cancelEditPass() {
                let t = this;
                t.addEditCategoryModal.show = false;
                t.addEditCategoryModal.saveLoading = false;
                t.cleanModal();
            },
            addBtn() {
                this.cleanModal();
                this.addEditCategoryModal.show = true;
            },
            getList() {
                let t = this;
                t.loading = true;
                t.$util.ajax.get('/admin/categories?search_data=' + JSON.stringify(t.search)).then(function (response) {
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
                t.$util.ajax.delete('/admin/categories/' + category).then(function (response) {
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
            handleSuccess(res, file) {
                let res_data = res.data;
                file.url = res_data.url;
                file.name = res_data.original_name;
                this.addEditCategoryForm.cover_image.attachment_id = res_data.attachment_id;
                this.addEditCategoryForm.cover_image.url = res_data.url;
            },
            handleFormatError(file) {
                this.$Notice.warning({
                    title: '文件格式不正确',
                    desc: '文件 ' + file.name + ' 格式不正确，请上传 jpg 或 png 格式的图片。'
                });
            },
            handleMaxSize(file) {
                this.$Notice.warning({
                    title: '超出文件大小限制',
                    desc: '文件 ' + file.name + ' 太大，不能超过 ' + this.uploadConfig.max_size + 'kb'
                });
            },
        },
    }
</script>
