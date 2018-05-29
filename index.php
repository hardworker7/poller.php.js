<html>
    <head>
        <title>Poller.js</title>
        <meta charset="UTF-8">
        <style>
            div.msg { margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <h1>Chat</h1>

        <div class="writepart" style="width: 50%; float: left;">
            <input type="text" id="pseudo" placeholder="choose your pseudo"> | <button id="go">go</button> <br>
            <textarea id="message" cols="32" rows="10" placeholder="your message"></textarea> <br>
            <button id="sendbtn">Envoyer</button>
        </div>

        <div class="chatpart" style="width: 50%; float: left;"></div>

        <script src="js/_poller.js"></script>
        <script>
            var pseudo = '';

            _poller.init('http://localhost:9000/server/', function () {
                _poller.start();

                _poller.on('hello', function (datas) {
                    alert(datas);
                    console.log(datas + ' is now online !');
                });

                _poller.on('message', function (datas) {
                    $datas = JSON.parse(datas);
                    newMessage($datas.message, $datas.sender);
                });
            });

            document.querySelector('#go').onclick = function () {
                if (!document.getElementById('pseudo').value.length) { alert("Pseudo ne peut être vide !"); return; }
                pseudo = document.getElementById('pseudo').value;
                _poller.emit('hello', pseudo);
            };

            document.querySelector('#sendbtn').onclick = function () {
                if (!pseudo.length) { alert("D'abord votre pseudo."); return}

                _poller.emit('message', JSON.stringify({sender: pseudo, message: document.querySelector('#message').value}));
                newMessage(document.querySelector('#message').value, pseudo);
            };

            function newMessage(message, pseudos) {
                var div = document.createElement('div');
                    div.className = 'msg';
                    div.innerHTML = '<b>' + (pseudos==pseudo ? 'Moi':pseudos) + '</b>: ' + message;
                document.querySelector('.chatpart').appendChild(div);
            }
        </script>
    </body>
</html>
