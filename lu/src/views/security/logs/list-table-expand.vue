<template>
    <div>
        <Col span="8">
            <Card>
                <Row class="expand-row">
                    <p class="margin-top-10">
                        <b>日志内容</b>
                        <transition name="publish-time">
                            <div class="publish-time-picker-con">
                                <div class="margin-top-10" v-for="(item,key) in row.content"> {{ key }}: &nbsp;&nbsp;
                                    <span class="green-color">{{ item }}</span>
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
