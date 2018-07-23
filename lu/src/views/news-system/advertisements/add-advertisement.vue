<template>
    <div id="news_system_advertisement-list">
        <Row>
            <Col span="16">
                <Card>
                    <p slot="title">
                        <Icon type="android-create"></Icon>
                        添加广告
                    </p>
                    <Form ref="addAdvertisementForm" :model="addAdvertisementForm" :rules="ruleEdit"
                          label-position="right" :label-width="80">
                        <FormItem label="广告标题" prop="name">
                            <Input v-model="addAdvertisementForm.name"></Input>
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
                                        name="advertisement"
                                        type="drag"
                                        :action="uploadConfig.uploadUrl"
                                        style="display: inline-block;width:58px;"
                                >
                                    <div style="width: 58px;height:58px;line-height: 58px;">
                                        <Icon type="camera" size="20"></Icon>
                                    </div>
                                </Upload>
                                <img :src="addAdvertisementForm.cover_image.url" alt=""
                                     v-if="addAdvertisementForm.cover_image.url" class="normall-image">
                            </div>
                        </FormItem>
                        <FormItem label="是否启用">
                            <RadioGroup v-model="addAdvertisementForm.enable">
                                <Radio label="F">禁用</Radio>
                                <Radio label="T">启用</Radio>
                            </RadioGroup>
                        </FormItem>
                        <FormItem label="广告内容">
                            <textarea id="advertisementEditor"></textarea>
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
                        <b>广告位：</b>
                        <Select v-model="addAdvertisementForm.advertisement_positions_id" filterable
                                @on-change="positionHasChanged"
                                placeholder="请选择文章标签" style="width:70%">
                            <Option v-for="(item,key) in advertisement_positions_ids" :value="item.id" :key="key">{{
                                item.name }}
                            </Option>
                        </Select>
                    </p>
                    <p class="margin-top-10">
                        <Icon type="link" class="margin-icon"></Icon>
                        <b>链接地址：</b>
                        <Input style="width:70%" v-model="addAdvertisementForm.link_url"
                               placeholder="请输入链接地址如： http://lucms.com"></Input>
                    </p>
                    <p class="margin-top-10">
                        <Icon type="ribbon-b" class="margin-icon"></Icon>
                        <b>排序：</b>
                        <Input style="width:30%" v-model="addAdvertisementForm.weight"
                               placeholder="请输入序号"></Input>
                    </p>
                    <p class="margin-top-10">
                        <Icon type="ios-time" class="margin-icon"></Icon>
                        <b>有效期：</b>
                        <DatePicker type="datetimerange"  placement="bottom-end" placeholder="请选择有效期，不选永久有效" confirm @on-clear="timeClear" @on-change="timeChanged" style="width: 60%"></DatePicker>
                    </p>
                    <p class="margin-top-10" v-if="typeIsModel">
                        <Icon type="ios-fastforward"></Icon>
                        <b>键值对选择：</b>
                        <transition name="publish-time">
                            <div class="publish-time-picker-con">
                                <div class="margin-top-10"> 模型 &nbsp;&nbsp;
                                    <Input type="text" size="small" style="width:80%"
                                           v-model="addAdvertisementForm.model_column_value.model"
                                           placeholder="如：App\Models\Article"></Input>
                                </div>
                                <div class="margin-top-10"> 字段 &nbsp;&nbsp;
                                    <Input type="text" size="small" style="width:80%"
                                           v-model="addAdvertisementForm.model_column_value.column" placeholder="如：slug"></Input>
                                </div>
                                <div class="margin-top-10"> 字段值
                                    <Input type="text" size="small" style="width:80%"
                                           v-model="addAdvertisementForm.model_column_value.value" placeholder="mark-down-preview"></Input>
                                </div>
                            </div>
                        </transition>
                    </p>
                    <Row class="margin-top-20 publish-button-con">
                        <span class="publish-button">
                            <Button  :loading="loading" @click="handleSubmit" icon="ios-checkmark" style="width:90px;" type="primary"> 保存 </Button>
                        </span>
                    </Row>
                </Card>
            </Col>
        </Row>
    </div>
</template>
<script>
    import tinymce from 'tinymce';

    export default {
        data() {
            return {
                advertisement_positions_ids: [],
                loading: false,
                addAdvertisementForm: {
                    advertisement_positions_id: 0,
                    advertisement_positions_type: '',
                    enable: 'F',
                    weight: '20',
                    link_url: '',
                    start_at: '',
                    end_at: '',
                    cover_image: {
                        attachment_id: 0,
                        url: '',
                    },
                    model_column_value: {
                        model: '',
                        column: '',
                        value: '',
                    },
                    content: '',
                },
                ruleEdit: {
                    name: [
                        {required: true, message: '请填写广告标题', trigger: 'blur'}
                    ],
                },
                uploadConfig: {
                    headers: {
                        'Authorization': localStorage.access_token_type + ' ' + localStorage.access_token,
                    },
                    format: ['jpg', 'jpeg', 'png', 'gif'],
                    tinymceFormat: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
                    max_size: 800,
                    uploadUrl: window.uploadUrl.uploadAdvertisement
                },
            }
        },
        computed: {
            typeIsModel() {
                return (this.addAdvertisementForm.advertisement_positions_type == 'model') ? true : false;
            },
        },
        created() {
            let t = this;
            t.getAdvertisementPositions();
        },
        mounted() {
            let t = this;
            t.tinymceInit();
        },
        destroyed() {
            tinymce.get('advertisementEditor').destroy();
        },
        methods: {
            getAdvertisementPositions() {
                let t = this;
                t.$util.ajax.get('/admin/advertisement_positions/all').then(function (response) {
                    let response_data = response.data;
                    t.advertisement_positions_ids = response_data.data;
                }, function (error) {
                })
            },
            positionHasChanged() {
                let t = this;
                var key = t.addAdvertisementForm.advertisement_positions_id;
                t.addAdvertisementForm.advertisement_positions_type = t.advertisement_positions_ids[key].type;
            },
            timeChanged: function (value, date_type) {
                let t = this;
                t.addAdvertisementForm.start_at = value[0];
                t.addAdvertisementForm.end_at = value[1];
            },
            timeClear() {
                let t = this;
                t.addAdvertisementForm.start_at = '';
                t.addAdvertisementForm.end_at = '';
            },
            handleSubmit() {
                let t = this;
                t.addAdvertisementForm.content = tinymce.get('advertisementEditor').getContent();
                t.$refs.addAdvertisementForm.validate((valid) => {
                    if (valid) {
                        t.loading = true;
                        this.$util.ajax.post('/admin/advertisements', t.addAdvertisementForm).then(function (response) {
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
            handleSuccess(res, file) {
                let res_data = res.data;
                file.url = res_data.url;
                file.name = res_data.original_name;
                this.addAdvertisementForm.cover_image.attachment_id = res_data.attachment_id;
                this.addAdvertisementForm.cover_image.url = res_data.url;
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
                    selector: '#advertisementEditor',
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

        },
    }

</script>
