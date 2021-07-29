<html>
    <head>
        <style type="text/css">
            html, body {
                height: 100%;
                margin: 0;
            }
            #app {
                max-width: 500px;
                height: 100%;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
            }
            .content {
                flex: 1;
                background-color: #ddd;
            }
            .footer {
                height: 50px;
            }
            .content .msg {
                float: left;
            }
            .content .msg.mime {
                float: right;
            }
            .footer > input {
                width: 100%;
                height: 40px;
                line-height: 1;
            }
            .clearfix:after {
                visibility: hidden;
                display: block;
                font-size: 0;
                content: " ";
                clear: both;
                height: 0;
            }
        </style>
    </head>

    <body>
        <div id="app">
            <div class="content">
                <div v-for="log in logs" class="clearfix">
                    <div v-if="log.is_mime" class="msg mime">{{ log.msg }}</div>
                    <div v-else class="msg">{{ log.msg }}</div>
                </div>
            </div>
            <div class="footer">
                <input type="text" v-model="message" v-on:keyup.enter="send">
            </div>
        </div>

        <script src="//cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <script>
            var app = new Vue({
                el: '#app',
                data: {
                    logs: [],
                    message: '',
                    webSocketObj: null,
                },
                created() {
                    this.initWebsocket()
                },
                methods: {
                    send() {
                        let msg = {
                            "op": "user:send",
                            "args": [1, this.message]
                        }
                        this.webSocketObj.send(JSON.stringify(msg))

                        this.logs.push({
                            "is_mime": true,
                            "msg": this.message
                        })
                        this.message = ''
                    },
                    initWebsocket() {
                        this.webSocketObj = new WebSocket('ws://192.168.69.128:9502/websocket?token=2')
                        this.webSocketObj.onmessage = this.onMessage
                        this.webSocketObj.onopen = this.onOpen
                        this.webSocketObj.onerror = this.onError
                        this.webSocketObj.onclose = this.onClose
                    },
                    onMessage(e) {
                        console.log(e)
                        let data = JSON.parse(e.data)
                        this.logs.push({
                            "is_mime": false,
                            "msg": data.payload
                        })
                    },
                    onOpen() {
                        console.log("websocket open")
                    },
                    onError() {
                        console.log("websocket error")
                    },
                    onClose() {
                        console.log("websocket close")
                    },
                }
            })
        </script>
    </body>
</html>