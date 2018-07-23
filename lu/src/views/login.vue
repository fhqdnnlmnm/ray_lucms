<style lang="less">
    @import '~@styles/login.less';
</style>

<template>
    <div class="login" @keydown.enter="handleLogin">
        <div class="login-con">
            <Card :bordered="false">
                <p slot="title">
                    <Icon type="log-in"></Icon>
                    欢迎登录
                </p>
                <div class="form-con">
                    <Form ref="loginForm" :model="formLogin" :rules="ruleLogin">
                        <FormItem prop="email">
                            <Input v-model="formLogin.email" placeholder="请输入用邮箱">
                            <span slot="prepend">
                                    <Icon :size="16" type="person"></Icon>
                                </span>
                            </Input>
                        </FormItem>
                        <FormItem prop="password">
                            <Input type="password" v-model="formLogin.password" placeholder="请输入密码">
                            <span slot="prepend">
                                    <Icon :size="14" type="locked"></Icon>
                                </span>
                            </Input>
                        </FormItem>
                        <FormItem>
                            <Button type="primary" :loading="loading" @click="handleLogin">
                                <span v-if="!loading">登录</span>
                                <span v-else>正在登录...</span>
                            </Button>
                        </FormItem>
                    </Form>
                    <p class="login-tip">没有账号？联系管理员</p>
                </div>
            </Card>
        </div>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                loading: false,
                formLogin: {
                    email: 'dev@lucms.com',
                    password: ''
                },
                ruleLogin: {
                    email: [
                        {required: true, message: '邮箱不能为空', trigger: 'blur'}
                    ],
                    password: [
                        {required: true, message: '密码不能为空', trigger: 'blur'}
                    ]
                }
            };
        },
        methods: {
            handleLogin() {
                let t = this;
                t.$refs.loginForm.validate((valid) => {
                    if (valid) {
                        t.loading = true;
                        t.$store.dispatch('login', t.formLogin).then(function (data) {
                            t.$util.ajax.defaults.headers.common['Authorization'] = localStorage.access_token_type + ' ' + localStorage.acces_token;
                            t.$Notice.success({
                                'title': '登录成功'
                            });
                            t.$router.push({
                                name: 'home_index'
                            });
                        }, function (error) {
                            t.$Notice.warning({
                                title: '出错了',
                                desc: error.message
                            });
                            t.loading = false;
                        });
                    }
                });
            }
        }
    };
</script>

<style>

</style>
