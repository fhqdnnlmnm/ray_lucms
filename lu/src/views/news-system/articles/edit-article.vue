<template>
    <div id="news_system_advertisement-list">
        <Row>
            <Col span="16">
                <Card>
                    <p slot="title">
                        <Icon type="android-create"></Icon>
                        更新文章
                    </p>
                    <Form ref="addArticleForm" :model="addArticleForm" :rules="ruleEditArticle"
                          label-position="right" :label-width="80">
                        <FormItem label="标题" prop="title">
                            <Input v-model="addArticleForm.title"></Input>
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
                                        :action="uploadConfig.uploadUrl"
                                        name="other"
                                        type="drag"
                                        style="display: inline-block;width:58px;"
                                >
                                    <div style="width: 58px;height:58px;line-height: 58px;">
                                        <Icon type="camera" size="20"></Icon>
                                    </div>
                                </Upload>
                                <img :src="addArticleForm.cover_image.url" alt=""
                                     v-if="addArticleForm.cover_image.url" class="normall-image">
                            </div>
                        </FormItem>
                        <FormItem label="是否启用">
                            <RadioGroup v-model="addArticleForm.enable">
                                <Radio label="F">禁用</Radio>
                                <Radio label="T">启用</Radio>
                            </RadioGroup>
                        </FormItem>
                        <FormItem label="关键词" prop="keywords">
                            <Input type="textarea" v-model="addArticleForm.keywords"
                                   placeholder="以英文逗号隔开"></Input>
                        </FormItem>
                        <FormItem label="描述" prop="description">
                            <Input type="textarea" v-model="addArticleForm.description"
                                   placeholder="请输入描述"></Input>
                        </FormItem>
                        <FormItem label="文章内容">
                            <textarea id="addArticleEditor"></textarea>
                        </FormItem>

                    </Form>
                </Card>
            </Col>

            <Col span="8" class="padding-left-10">
                <Card>
                    <p slot="title">
                        <Icon type="paper-airplane"></Icon>
                        其它信息
                    </p>
                    <p class="margin-top-10">
                        <Icon type="levels" class="margin-icon"></Icon>
                        <b>分类</b>
                        <Select v-model="addArticleForm.category_id" filterable placeholder="请选择文章分类" style="width:70%">
                            <Option v-for="(item,key) in articleCategoryList" :value="item.id" :key="key">{{ item.name }} </Option>
                        </Select>
                    </p>
                    <p class="margin-top-10">
                        <Icon type="ribbon-b" class="margin-icon"></Icon>
                        <b>排序：</b>
                        <Input style="width:30%" v-model="addArticleForm.weight"
                               placeholder="请输入序号"></Input>
                    </p>
                    <p class="margin-top-10">
                        <Icon type="ios-rose" class="margin-icon"></Icon>
                        <b>置顶：</b>
                        <Select size="small" style="width:20%" v-model="addArticleForm.top">
                            <Option value="F">否</Option>
                            <Option value="T">是</Option>
                        </Select>
                    </p>
                    <p class="margin-top-10">
                        <Icon type="home" class="margin-icon"></Icon>
                        <b>推荐：</b>
                        <Select size="small" style="width:20%" v-model="addArticleForm.recommend">
                            <Option value="F">否</Option>
                            <Option value="T">是</Option>
                        </Select>
                    </p>

                    <p class="margin-top-10">
                        <Icon type="eye"></Icon>&nbsp;&nbsp;公开度：&nbsp;<b>{{ Openness }}</b>
                        <Button v-show="!editOpenness" size="small" @click="handleEditOpenness" type="text">修改</Button>
                        <transition name="openness-con">
                            <div v-show="editOpenness" class="publish-time-picker-con">
                                <RadioGroup v-model="addArticleForm.access_type" vertical>
                                    <Radio label="PUB"> 公开</Radio>
                                    <Radio label="PWD"> 密码
                                        <Input v-show="addArticleForm.access_type === 'PWD'"
                                               v-model="addArticleForm.access_value" style="width:50%"
                                               size="small" placeholder="请输入密码"/>
                                    </Radio>
                                    <Radio label="PRI">私密</Radio>
                                </RadioGroup>
                                <div>
                                    <Button type="primary" @click="handleSaveOpenness">确认</Button>
                                </div>
                            </div>
                        </transition>
                    </p>
                    <Row class="margin-top-20 publish-button-con">
                        <span class="publish-button right-float">
                            <Button :loading="loading" @click="handleSubmit" icon="ios-checkmark"
                                    type="primary"> 保存 </Button>
                        </span>
                    </Row>
                </Card>

                <div class="margin-top-10">
                    <Card>
                        <p slot="title">
                            <Icon type="ios-pricetags-outline"></Icon>
                            标签
                        </p>
                        <Row>
                            <Col span="18">
                                <Select v-model="addArticleForm.tags" multiple filterable placeholder="请选择文章标签">
                                    <Option v-for="item in articleTagList" :value="item.id" :key="item.id">{{ item.name
                                        }}
                                    </Option>
                                </Select>
                            </Col>
                            <Col span="6" class="padding-left-10">
                                <Button @click="handleAddNewTag" long type="ghost">新建</Button>
                            </Col>
                        </Row>
                    </Card>
                </div>
            </Col>
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
                <Button type="text" @click="handleCancelAddTag">取消</Button>
                <Button type="primary" @click="handleAddTag" :loading="addEditTagModal.saveLoading">保存</Button>
            </div>
        </Modal>
    </div>
</template>
<script>
    import tinymce from 'tinymce';

    export default {
        data() {
            return {
                editOpenness: false,
                Openness: '公开',
                loading: false,
                articleCategoryList: [],
                articleTagList: [],
                addArticleForm: {
                    id: this.$route.params.article_id,
                    tags: 0,
                    content: '',
                    category_id: 0,
                    enable: 'F',
                    weight: '20',
                    recommend: 'F',
                    top: 'F',
                    cover_image: {
                        attachment_id: 0,
                        url: '',
                    },
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
                ruleEditArticle: {
                    title: [
                        {required: true, message: '请填写文章标题', trigger: 'blur'}
                    ],
                },
                uploadConfig: {
                    headers: {
                        'Authorization': localStorage.access_token_type + ' ' + localStorage.access_token,
                    },
                    format: ['jpg', 'jpeg', 'png', 'gif'],
                    tinymceFormat: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
                    max_size: 500,
                    uploadUrl: window.uploadUrl.uploadOther,
                    data: {
                        max_width: 800
                    }
                },
            }
        },
        created() {
            let t = this;
            t.getArticleCategories();
            t.getArticleTags();
            t.getArticleInfo(t.addArticleForm.id);
        },
        mounted() {
            let t = this;
            t.tinymceInit();
        },
        destroyed() {
            tinymce.get('addArticleEditor').destroy();
        },
        methods: {
            getArticleCategories() {
                let t = this;
                t.$util.ajax.get('/admin/categories/all').then(function (response) {
                    let response_data = response.data;
                    t.articleCategoryList = response_data.data;
                }, function (error) {
                })
            },
            getArticleTags() {
                let t = this;
                t.$util.ajax.get('/admin/tags').then(function (response) {
                    let response_data = response.data;
                    t.articleTagList = response_data.data;
                }, function (error) {
                })
            },
            getArticleInfo(article) {
                let t = this;
                if (article > 0) {
                    t.$util.ajax.get('/admin/articles/' + article).then(function (response) {
                        let response_data = response.data;
                        tinymce.get('addArticleEditor').setContent(response_data.data.content);
                        t.addArticleForm = response_data.data;
                        t.handleSaveOpenness();
                        t.addArticleForm.cover_image.attachment_id = response_data.data.cover_image.attachment_id;
                        t.addArticleForm.tags = response_data.data.tagids;
                    }, function (error) {
                        t.$Notice.warning({
                            title: '出错了',
                            desc: error.message
                        });
                    })
                }
            },
            handleSubmit() {
                let t = this;
                t.addArticleForm.content = tinymce.get('addArticleEditor').getContent();
                t.$refs.addArticleForm.validate((valid) => {
                    if (valid && this.passwordValidate()) {
                        t.loading = true;
                        t.$util.ajax.patch('/admin/articles/'+t.addArticleForm.id, t.addArticleForm).then(function (response) {
                            t.$Notice.success({
                                title: '操作成功'
                            });
                            t.loading = false;
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.loading = false;
                        })
                    }
                });
            },
            handleEditOpenness() {
                this.editOpenness = !this.editOpenness;
            },
            handleSaveOpenness() {
                var access_type = this.addArticleForm.access_type;
                if (this.passwordValidate()) {
                    this.Openness = (access_type === 'PUB') ? '公开' : (access_type === 'PWD') ? '密码' : '私密';
                    this.editOpenness = false;
                }
            },
            passwordValidate() {
                var access_type = this.addArticleForm.access_type;
                var access_value = this.addArticleForm.access_value;
                if (access_type === 'PWD') {
                    var patt = /^[a-zA-Z0-9]{4,8}$/;
                    if (!patt.test(access_value)) {
                        this.$Notice.warning({
                            title: '出错了',
                            desc: '密码只能是4到8位的数字与字母'
                        });
                        return false;
                    }

                }
                return true;
            },
            handleSuccess(res, file) {
                let res_data = res.data;
                file.url = res_data.url;
                file.name = res_data.original_name;
                this.addArticleForm.cover_image.attachment_id = res_data.attachment_id;
                this.addArticleForm.cover_image.url = res_data.url;
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
            tinymceInit() {
                let t = this;
                tinymce.init({
                    selector: '#addArticleEditor',
                    branding: false,
                    elementpath: false,
                    height: 600,
                    language: 'zh_CN.GB2312',
                    menubar: 'edit insert view format table tools',
                    theme: 'modern',
                    plugins: [
                        'advlist autolink lists link image charmap print preview hr anchor pagebreak imagetools',
                        'searchreplace visualblocks visualchars code fullscreen fullpage',
                        'insertdatetime media nonbreaking save table contextmenu directionality',
                        'emoticons paste textcolor colorpicker textpattern imagetools codesample'
                    ],
                    toolbar1: ' newnote print fullscreen preview | undo redo | insert | styleselect | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image emoticons media codesample',
                    autosave_interval: '20s',
                    image_advtab: true,
                    table_default_styles: {
                        width: '100%',
                        borderCollapse: 'collapse'
                    },
                    images_upload_url: window.uploadUrl.tinymceUpload,
                    images_upload_handler: function (blobInfo, success, failure) {
                        if (blobInfo.blob().size / 1000 > t.uploadConfig.max_size) {
                            failure('文件大小不能超过' + t.uploadConfig.max_size + 'kb');
                        }

                        if (t.uploadConfig.tinymceFormat.indexOf(blobInfo.blob().type) > 0) {
                            uploadPic()
                        } else {
                            failure('图片格式错误')
                        }

                        function uploadPic() {
                            let formData = new FormData();
                            formData.append('tinymce', blobInfo.blob(), blobInfo.filename());
                            t.$util.ajax({
                                method: 'POST',
                                url: window.uploadUrl.tinymceUpload,
                                data: formData,
                            }).then(function (response) {
                                success(response.data.data.url);
                            }, function (error) {
                                failure(error.message)
                            })
                        }
                    },


                });
            },
            handleAddNewTag() {
                this.addEditTagModal.show = true;
            },
            handleAddTag() {
                let t = this;
                t.$refs.addEditTagForm.validate((valid) => {
                    if (valid) {
                        t.addEditTagModal.saveLoading = true;
                        t.$util.ajax.post('/admin/tags', t.addEditTagForm).then(function (response) {
                            let response_data = response.data;
                            t.$Notice.success({
                                title: response_data.message
                            });
                            t.getArticleTags();
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
            handleCancelAddTag() {
                this.addEditTagModal.show = false;
            }

        },
    }

</script>
