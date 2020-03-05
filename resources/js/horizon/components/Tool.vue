<script>
    export default {
        data: function () {
            return {
                path: "",
            };
        },
        created() {
            this.getPath();
        },
        computed: {
            horizonPath: function () {
                return ("/" + this.path).replace("\/\/", "\/");
            },
        },
        methods: {
            getPath: function () {
                let self = this;
                Nova.request().get("/nova-vendor/maia-horizon/path")
                    .then(function (response) {
                        self.path = response.data;
                    });
            },
            iframeStyles() {
                let frame = this.$refs.iframeContent;
                frame.contentDocument.documentElement.childNodes[2].style.background = 'transparent';
                frame.contentDocument.documentElement.childNodes[2].style.padding = '3.125rem';
            }
        },
    }
</script>

<template>
    <iframe ref="iframeContent" :src="horizonPath" @load="iframeStyles" class="w-full" frameborder="0" scrolling="auto"></iframe>
</template>

<style scoped lang="scss">
    iframe {
        margin-top: -50px;
        height: 100vh;
    }
</style>