<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text to Speech</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.0.0-beta1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div id="app" class="container mt-5">
        <div class="row mb-3">
            <!-- 输入区 -->
            <div class="col-md-6">
                <label for="text" class="form-label">输入待转文本</label>
                <textarea class="form-control" id="text" rows="6" v-model="text"></textarea>
            </div>

            <!-- 选择区 -->
            <div class="col-md-6">
                <label for="voice">音色选择</label>
                <select class="form-control" id="voice" v-model="voice">
                    <option value="alloy">Alloy</option>
                    <option value="echo">Echo</option>
                    <option value="fable">Fable</option>
                    <option value="onyx">Onyx</option>
                    <option value="nova">Nova</option>
                    <option value="shimmer">Shimmer</option>
                </select>
                <label for="model">质量</label>
                <select class="form-control" id="model" v-model="model">
                    <option value="tts-1">普通</option>
                    <option value="tts-1-hd">高清</option>
                </select>
                <label for="response_format">返回格式</label>
                <select class="form-control" id="response_format" v-model="response_format">
                    <option value="opus">opus</option>
                    <option value="mp3">mp3</option>
                    <option value="aac">aac</option>
                    <option value="flac">flac</option>
                    <option value="wav">wav</option>
                    <option value="pcm">pcm</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <!-- 转换区 -->
                <button class="btn btn-primary" @click="convertText">转换文本</button>
                <div v-if="convertedText">
                    <p>已转的文本: {{ convertedText }}</p>
                    <button class="btn btn-success" @click="download">下载</button>
                    <button class="btn btn-secondary" @click="listen">试听</button>
                </div>
            </div>
        </div>


    </div>

    <!-- <script src="https://cdn.bootcdn.net/ajax/libs/vue/2.6.12/vue.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/layer/3.1.1/layer.js"></script>
    <script>
        // app.js
        new Vue({
            el: "#app",
            data: {
                text: "",
                voice: "alloy",
                model: "tts-1",
                response_format: "mp3",
                convertedText: "",
                audioUrl: "",
            },
            methods: {
                convertText() {
                    var load = layer.load(0, {
                        shade: [0.5, '#fff']
                    });
                    axios
                        .post(
                            "./tts.php", {
                                voice: this.voice,
                                model: this.model,
                                response_format: this.response_format,
                                text: this.text,
                            }, {
                                headers: {
                                    "Content-Type": "application/json", // Explicitly set the content type
                                },
                            }
                        )
                        .then((response) => {
                            layer.close(load);
                            if (response.data.error) {
                                layer.msg(response.data.error);
                                return;
                            }
                            const data = response.data;
                            if (data.code == 0) {
                                this.audioUrl = data.url;
                                this.convertedText = this.text;
                                localStorage.setItem("convertedText", this.convertedText);
                                localStorage.setItem("audioUrl", this.audioUrl);
                                return;
                            }
                            layer.msg(data.msg);
                        });
                },

                download() {
                    const a = document.createElement("a");
                    a.href = this.audioUrl;
                    a.download = "converted_audio.mp3";
                    a.click();
                },
                listen() {
                    const audio = new Audio(this.audioUrl);
                    audio.play();
                },
            },
            mounted() {
                this.convertedText = localStorage.getItem("convertedText");
                this.audioUrl = localStorage.getItem("audioUrl");
            },
        });
    </script>
</body>

</html>