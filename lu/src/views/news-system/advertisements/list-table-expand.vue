<template>
    <div>
        <Col span="8">
            <Card>
                <Row class="expand-row">
                    <span class="expand-key expand-title">跳转链接:&nbsp;&nbsp;&nbsp; </span>
                    <a :href="row.link_url" target="_blank">{{ row.link_url }}</a>
                </Row>
                <Row class="expand-row">
                    <p class="margin-top-10">
                        <b>键值对：</b>
                        <transition name="publish-time">
                            <div class="publish-time-picker-con">
                                <div class="margin-top-10"> 模型： &nbsp;&nbsp;
                                    <span class="green-color" v-if="row.model_column_value.model">{{ row.model_column_value.model }}</span>
                                    <span v-else> -- </span>
                                </div>
                                <div class="margin-top-10"> 字段： &nbsp;&nbsp;
                                    <span class="green-color" v-if="row.model_column_value.column">{{ row.model_column_value.column }}</span>
                                    <span v-else> -- </span>
                                </div>
                                <div class="margin-top-10"> 字段值：
                                    <span class="green-color" v-if="row.model_column_value.value">{{ row.model_column_value.value }}</span>
                                    <span v-else> -- </span>
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
