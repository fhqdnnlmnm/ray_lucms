<style lang="less">
    @import '~@styles/upload.less';
</style>
<template>
    <div>
        <Col span="16">
            <Card>
                <p slot="title">
                    <Icon type="edit"></Icon>
                    添加用户
                </p>
                <div>
                    <Form ref="editUserForm" :model="editUserForm" :rules="ruleEditUser"
                          label-position="right">
                        <FormItem label="昵称：" prop="name">
                            <div style="display:inline-block;width:50%">
                                <Input v-model="editUserForm.name"></Input>
                            </div>
                        </FormItem>
                        <FormItem label="邮箱：" prop="email">
                            <div style="display:inline-block;width:50%">
                                <Input  v-model="editUserForm.email"></Input>
                            </div>
                        </FormItem>
                        <FormItem label="登录密码：" prop="password">
                            <div style="display:inline-block;width:50%">
                                <Input type="password" v-model="editUserForm.password"></Input>
                            </div>
                        </FormItem>
                        <FormItem label="登录密码确认：" prop="password_confirmation">
                            <div style="display:inline-block;width:50%">
                                <Input type="password" v-model="editUserForm.password_confirmation"></Input>
                            </div>
                        </FormItem>
                        <FormItem label="是否可登录后台">
                            <RadioGroup v-model="editUserForm.is_admin">
                                <Radio label="F">否</Radio>
                                <Radio label="T">是</Radio>
                            </RadioGroup>
                        </FormItem>
                        <FormItem label="头像：">
                            <div style="display:inline-block;width:50%">
                                <Upload
                                        :show-upload-list="false"
                                        :on-success="handleSuccess"
                                        :on-format-error="handleFormatError"
                                        :on-exceeded-size="handleMaxSize"
                                        :headers="uploadConfig.headers"
                                        :max-size="uploadConfig.max_size"
                                        :format="uploadConfig.format"
                                        name="avatar"
                                        type="drag"
                                        :action="uploadConfig.uploadUrl"
                                        style="display: inline-block;width:58px;"
                                >
                                    <div style="width: 58px;height:58px;line-height: 58px;">
                                        <Icon type="camera" size="20"></Icon>
                                    </div>
                                </Upload>
                                <img class="head_image" :src="editUserForm.head_image.url" alt=""
                                     v-if="editUserForm.head_image.url">
                            </div>
                        </FormItem>
                        <FormItem>
                            <Button type="primary" :loading="loading" @click="handleSubmit">保存</Button>
                        </FormItem>

                    </Form>
                </div>
            </Card>
        </Col>
    </div>
</template>
<script>
    export default {
        data() {
            const validatePassword = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入登录密码'));
                } else {
                    if (this.editUserForm.password !== '') {
                        // 对第二个密码框单独验证
                        this.$refs.editUserForm.validateField('password_confirmation');
                    }
                    callback();
                }
            };
            const validatePasswordConfirm = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入确认密码'));
                } else if (value !== this.editUserForm.password) {
                    callback(new Error('两次密码不一致 '));
                } else {
                    callback();
                }
            };
            return {
                loading: false,
                editUserForm: {
                    name: '',
                    is_admin: 'F',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    head_image: {
                        attachment_id: 0,
                        url: '',
                    },
                },
                ruleEditUser: {
                    name: [
                        {required: true, message: '请填写昵称', trigger: 'blur'},
                        {type: 'string', min: 2, message: '昵称至少要 2 个字符', trigger: 'blur'}
                    ],
                    email: [
                        {required: true, message: '请填写邮箱', trigger: 'blur'},
                        {type: 'email', message: '邮箱格式不正确', trigger: 'blur'},
                    ],
                    password: [
                        {validator: validatePassword, trigger: 'blur'}
                    ],
                    password_confirmation: [
                        {validator: validatePasswordConfirm, trigger: 'blur'}
                    ],
                },
                uploadConfig: {
                    headers: {
                        'Authorization': localStorage.access_token_type + ' ' + localStorage.access_token,
                    },
                    format: ['jpg', 'jpeg', 'png'],
                    max_size: 500,
                    uploadUrl: window.uploadUrl.uploadAvatar
                },
            }
        },
        methods: {
            handleSubmit() {
                let t = this;
                t.$refs.editUserForm.validate((valid) => {
                    if (valid) {
                        t.loading = true;
                        this.$util.ajax.post('/admin/users', t.editUserForm).then(function (response) {
                            t.$Notice.success({
                                title: '资料添加成功'
                            });
                            t.loading = false;
                        }, function (error) {
                            t.loading = false;
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                        })
                    }
                });
            },
            handleSuccess(res, file) {
                let res_data = res.data;
                file.url = res_data.url;
                file.name = res_data.original_name;
                this.editUserForm.head_image.attachment_id = res_data.attachment_id;
                this.editUserForm.head_image.url = res_data.url;
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
        }
    }

</script>
