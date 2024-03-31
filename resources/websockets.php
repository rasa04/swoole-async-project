<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Websockets server</title>
</head>
<body>
    <h1>My websockets client</h1>
    <div>
        <form id="message-form" action="">
            <div>
                <label>
                    <input id="message-box" type="text" placeholder="The message goes here...">
                </label>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>
    <div>
        <ul id="output"></ul>
    </div>
    <script>
        (function () {
            const app = {
                ws: null,

                config: {
                    uri: 'ws://127.0.0.1',
                    port: 8585,
                },

                init: () => {
                    app.connectToServer()
                    app.listenEvents()
                },

                connectToServer: () => {
                    const wsServer = app.config.uri + ':' + app.config.port;
                    app.ws = new WebSocket(wsServer);

                    app.ws.onopen = function (event) {
                        console.log('Connected to websockets server.');
                    };

                    app.ws.onclose = function (event) {
                        console.log('Disconnected.');
                    };

                    app.ws.onmessage = function (event) {
                        console.log('Received data from server: ' + event.data);
                        app.handleIncomingMessage(event.data);
                    };

                    app.ws.onerror = function (event, error) {
                        console.log('Error occurred: ' + event.data);
                    };
                },

                listenEvents: () => {
                    document.getElementById('message-form').addEventListener('submit', app.handleFormSubmit, false);
                },

                handleFormSubmit: (e) => {
                    e.preventDefault();
                    app.ws.send(document.getElementById('message-box').value);
                },

                handleIncomingMessage: (data) => {
                    let input = document.createElement('li');
                    input.innerText = data;
                    document.getElementById('output').appendChild(input);
                },
            };

            app.init()
        })()
    </script>
</body>
</html>
