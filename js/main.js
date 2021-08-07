var limit = 24;
var skip = 0;

function getCurrentPrice(currencyCode, callbackSuccess, callbackFailure) {
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (this.readyState === this.DONE) {
            var response = null;

            try {
                response = JSON.parse(this.responseText);
            } catch(err) {}

            switch (this.status) {
                case Cryptomaniacos.Constants.HTTP_STATUS_OK:
                    callbackSuccess(response);
                    break;
            
                default:
                    callbackFailure(response, this.status);
                    break;
            }
        }
    }

    xhr.open('GET', 'https://api.coinstats.app/public/v1/coins/'+currencyCode+'?currency=USD');

    xhr.withCredentials = false;

    xhr.send();
}

function getCoins(limit, skip, callbackSuccess, callbackFailure) {
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (this.readyState === this.DONE) {
            var response = null;

            try {
                response = JSON.parse(this.responseText);
            } catch(err) {}

            switch (this.status) {
                case Cryptomaniacos.Constants.HTTP_STATUS_OK:
                    callbackSuccess(response);
                    break;
            
                default:
                    callbackFailure(response, this.status);
                    break;
            }
        }
    }

    xhr.open('GET', 'https://api.coinstats.app/public/v1/coins?skip='+skip+'&limit='+limit+'&currency=USD');

    xhr.withCredentials = false;

    xhr.send();
}

var inputHandler = null;
var object = {};

if (document.getElementById('comment-form')) {
    document.getElementById('criptoactivo-list').addEventListener('submit', function (event) {
        var payLoad = {};
        var payLoadForm = null;

        this.querySelectorAll('input,textarea').forEach(function (input) {
            payLoad[input.name] = input.value;
        });

        var xhr = new XMLHttpRequest();

        switch (this.getAttribute('format')) {
            case 'json':
                payLoadForm = JSON.stringify(payLoad);
                break
        
            default:
                return;
        }

        xhr.open('POST', 'http://localhost/php/public/comments/new/' + this.getAttribute('format'), true);

        xhr.send(payloadForm);

    });

}

if (document.getElementById('comment-list')) {
    
}

if (document.getElementById('criptoactivo-list')) {
    document.getElementById('criptoactivo-list').addEventListener('click', seeCripto);

    document.getElementById('criptoactivo-list').addEventListener('fill-coins', function (event) {
        var cryptoList = this;

        document.getElementById('pagination').dispatchEvent(new Event('toggle'));

        cryptoList.innerText = 'Cargando...';

        var success = function (coinArray) {
            document.getElementById('pagination').dispatchEvent(new Event('toggle'));
            var fragment = document.createDocumentFragment();

            coinArray.coins.forEach(function (coin) {
                var cripto = Cryptomaniacos.Elements.CriptoCard(coin.icon, coin.name, coin.websiteUrl, coin.twitterUrl, 
                    coin.id);

                fragment.appendChild(cripto);
            });

            cryptoList.innerText = '';
            cryptoList.appendChild(fragment);
        }

        getCoins(limit, skip, success, function () {
            cryptoList.innerText = 'Un error ha ocurrido. Por favor, recargue la página';
        });
    });

    document.getElementById('pagination').addEventListener('toggle', function (event) {
        for (var i = 0; i < this.children.length; i++) {
            this.children[i].disabled = !this.children[i].disabled;
        }
    });

    document.getElementById('pagination').addEventListener('click', function (event) {
        var element = event.target;

        if (element.name === 'next' || element.parentElement.name === 'next') {
            skip += limit;
        } else if (element.name === 'last' || element.parentElement.name === 'last') {
            var newSkip = skip - limit;

            if (newSkip < 0) {
                return;
            }

            skip = newSkip;
            
        } else {
            return;
        }

        document.getElementById('criptoactivo-list').dispatchEvent(new Event('fill-coins'));
    }, {
        capture: true,
    });

    document.getElementById('criptoactivo-list').addEventListener('click', function (event) {
        var element = event.target;

        if (!element.getAttribute('key')) {
            while(element && (element.parentElement != this || element.parentElement != null)) {
                element = element.parentElement;

                if (element && element.getAttribute('key')) {
                    break;
                }
            }

            if (element == null || !element.getAttribute('key')) return;
        }

        var criptoActivoModal = document.getElementById('criptoActivoModal');

        criptoActivoModal.querySelector('.modal-title').innerText = element.querySelector('.card-title').innerText;

        criptoActivoModal.querySelector('.img-fluid').setAttribute('src', 
            element.querySelector('img').getAttribute('src'));

        criptoActivoModal.querySelector('#websiteUrl').setAttribute('href', element.getAttribute('websiteUrl'));
        criptoActivoModal.querySelector('#twitterUrl').setAttribute('href', element.getAttribute('twitterUrl'));

        bootstrap.Modal.getOrCreateInstance(criptoActivoModal).show();
    }, {
        capture: true,
    });
}


document.addEventListener('DOMContentLoaded', function (event) {
    var fillCoins =  function (coinArray) {
        var fragment = document.createDocumentFragment();

        coinArray.coins.forEach(function(coin) {
            var optionElement = document.createElement('option');
            optionElement.innerText = coin.name;
            optionElement.value = coin.id;
            optionElement.setAttribute('symbol', coin.symbol);

            fragment.appendChild(optionElement);
        });

        document.getElementById('criptoactivo-select').innerText = '';
        document.getElementById('criptoactivo-select').appendChild(fragment);
        document.getElementById('criptoactivo-select').disabled = false;
        document.getElementById('criptoactivo-input').disabled = false;
        document.getElementById('criptoactivo-input-reverse').disabled = false;
        document.getElementById('criptoactivo-select').value = document.getElementById('criptoactivo-select').children[0].value;
        document.getElementById('criptoactivo-select').children[0].setAttribute('selected', '');
        document.getElementById('criptoactivo-select').dispatchEvent(new Event('change'));
    
    }

    if (document.getElementById('criptoactivo-select')) 
        getCoins(limit, skip, document.getElementById('criptoactivo-select').value, fillCoins, function(response, status) {
            console.log(status, response);
        });

    if (document.getElementById('criptoactivo-list')) {
        document.getElementById('criptoactivo-list').dispatchEvent(new Event('fill-coins'));
    }
});

if (document.getElementById('criptoActivoModal')) {
    document.getElementById('criptoActivoModal').addEventListener('hide.bs.modal', function () {
        var networkList = bootstrap.Collapse.getInstance(document.getElementById('network-list'));
        if (networkList) {
            networkList.hide();
        }
    });
}
function calculateEventListener() {
    var input = document.getElementById('criptoactivo-input');
    var inputReverse = document.getElementById('criptoactivo-input-reverse');
    var valueSelected = document.getElementById('criptoactivo-select').value;
    
    if (valueSelected) {
        document.getElementById('cripto-icon').innerText = 
            document.getElementById('resultado-symbol').innerText = ' ' +
            document.getElementById('criptoactivo-select')
            .querySelector('option[value="'+valueSelected+'"]')
            .getAttribute('symbol');
    }

    if (inputHandler)
        clearTimeout(inputHandler);
    
    function calculateExchange(response) {
        document.getElementById('resultado-conversion').innerText = 
            input.value ? input.value * response.coin.price : '0';

        
        document.getElementById('resultado-conversion-reverse').innerText = 
            inputReverse.value ? inputReverse.value / response.coin.price : '0';

        input.disabled = false;
        inputReverse.disabled = false;
    }
    
    inputHandler = setTimeout(function() {
        input.disabled = true;
        inputReverse.disabled = true;
    
        
        getCurrentPrice(document.getElementById('criptoactivo-select').value, calculateExchange
        , function (response, status) {
            input.disabled = false;
            inputReverse.disabled = false;
    
            console.log(response, status);

            Cryptomaniacos.Elements.Toast.show();
        })
    }, 300);
}

function seeCripto(event) {


}

if (document.getElementById('criptoactivo-input'))
    document.getElementById('criptoactivo-input').oninput = calculateEventListener;

if (document.getElementById('criptoactivo-input-reverse'))
    document.getElementById('criptoactivo-input-reverse').oninput = calculateEventListener;

if (document.getElementById('criptoactivo-select'))
    document.getElementById('criptoactivo-select').onchange = calculateEventListener;

var Cryptomaniacos = {
    Constants: {
        HTTP_STATUS_OK: 200,
    },

    Elements: {
        Toast: null,

        CriptoCard: function (imgSrc, name, websiteUrl, twitterUrl, idCoin) {
            var cardDiv = document.createElement('div');
            cardDiv.setAttribute('key', idCoin)
            cardDiv.setAttribute('websiteUrl', websiteUrl)
            cardDiv.setAttribute('twitterUrl', twitterUrl)
            cardDiv.classList.add('card', 'w-15', 'mt-2');

            var cardBody = document.createElement('div');
            cardBody.classList.add('card-body', 'justify-content-center', 'align-items-center');

            var cardImg = document.createElement('img');
            cardImg.width = '25'
            cardImg.height = '25'
            cardImg.src = imgSrc;

            var cardTitle = document.createElement('h5');
            cardTitle.classList.add('card-title');
            cardTitle.innerText = name;


            cardBody.appendChild(cardTitle);

            cardDiv.appendChild(cardImg);
            cardDiv.appendChild(cardBody);

            return cardDiv;
        }
    }
}