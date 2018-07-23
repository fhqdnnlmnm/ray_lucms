<template>
    <div>
        <Col span="15">
            <Card>
                <Row class="expand-row">
                    <span class="expand-key expand-title">绝对路径:  </span>
                    <span class="green-color">{{ row.storage_path }}/{{ row.storage_name}}</span>
                </Row>
                <Row class="margin-top-10">
                    <div v-if="attachmentIsImage">
                        <img :src="getAttachmentUrl" alt="" style="max-width: 100px;max-height: 100px"
                             @click="modalHeadImageShow">
                    </div>
                    <div v-else>
                        <span class="expand-key expand-title">访问 url:  </span>
                        <span class="expand-value">{{ row.domain }}/{{ row.link_path}}/{{ row.storage_name }}</span>
                    </div>
                </Row>
                <Modal v-model="modalHeadImage.show" width="1000px">
                    <p slot="header">图片预览</p>
                    <div class="text-center">
                        <img :src="getAttachmentUrl" alt="" v-if="modalHeadImage.show" class="center-align">
                    </div>
                    <div slot="footer">
                    </div>
                </Modal>
            </Card>
        </Col>
    </div>
</template>

<script>
    export default {
        props: {
            row: Object
        },
        data() {
            return {
                modalHeadImage: {
                    show: false
                }
            }
        },
        methods: {
            modalHeadImageShow() {
                this.modalHeadImage.show = true;
            }
        },
        computed: {
            getAttachmentUrl() {
                return this.row.domain + '/' + this.row.link_path + '/' + this.row.storage_name
            },
            attachmentIsImage() {
                return (this.row.mime_type.indexOf('image') === -1) ? false : true
            }
        }
    };
</script>
