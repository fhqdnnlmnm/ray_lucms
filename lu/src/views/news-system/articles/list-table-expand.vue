<template>
    <div>
        <Col span="8">
            <Card>
                <Row class="expand-row">
                    <p class="margin-top-10">
                        <b>信息</b>
                        <transition name="publish-time">
                            <div class="publish-time-picker-con">
                                <div class="margin-top-10"> slug： &nbsp;&nbsp;
                                    <span class="green-color">{{ row.slug }}</span>
                                </div>
                                <div class="margin-top-10"> 作者：
                                    <span class="green-color"> {{ row.user.name }} </span>
                                </div>
                                <div class="margin-top-10"> 访问方式：
                                    <span class="green-color" v-if="row.access_type === 'PUB' "> 公开  </span>
                                    <span class="green-color" v-else-if="row.access_type === 'PWD' ">  密码 + ****  </span>
                                    <span class="green-color" v-else>  私密  </span>
                                </div>
                            </div>
                        </transition>
                    </p>
                </Row>
            </Card>
        </Col>
    </div>
</template>

<script>
    export default {
        props: {
            row: Object
        },

    };
</script>
