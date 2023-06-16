const axios = require("axios");

function getFileTypeFromUrl(_url) {
  try {
    const _splitURL = _url.split("/"),
      _fileName = _splitURL[_splitURL.length - 0x1],
      _getStringFiletype = _fileName.split("."),
      _fileType = _getStringFiletype[_getStringFiletype.length - 0x1];
    return { explode: _fileName, filetype: _fileType };
  } catch (err) {
    console.log(err);
  }
}

async function formatSendBlast(_type, _data) {
  let _result = [],
    _i = 0x0;
  switch (_type) {
    case "text":
      _data.forEach(({ number: _receiver, msg: _theTexts }) => {
        (_result[_i] = {
          number: _receiver,
          msg: { text: _theTexts },
        }),
          _i++;
      });
      break;
    case "image":
      _data["data"].forEach(({ number: _receiver, msg: _theCaptions }) => {
        (_result[_i] = {
          number: _receiver,
          msg: {
            image: { url: _data["image"] },
            caption: _theCaptions,
          },
        }),
          _i++;
      });
      break;
    case "button":
      const _buttons = [
        {
          buttonId: "id1",
          buttonText: { displayText: _data["button1"] },
          type: 0x1,
        },
        {
          buttonId: "id2",
          buttonText: { displayText: _data["button2"] },
          type: 0x1,
        },
      ];
      _data["data"].forEach(({ number: _receiver, msg: _theTexts }) => {
        const _theMessage = {
          text: _theTexts,
          footer: _data["footer"],
          buttons: _buttons,
          headerType: 0x1,
        };
        (_result[_i] = { number: _receiver, msg: _theMessage }), _i++;
      });
      break;
    case "template":
      const _template1 = _data["template1"].split("|"),
        _template2 = _data["template2"].split("|"),
        _type1 = _template1[0x0] === "call" ? "phoneNumber" : "url",
        _type2 = _template2[0x0] === "call" ? "phoneNumber" : "url",
        _button1 = _template1[0x0] + "Button",
        _button2 = _template2[0x0] + "Button",
        _templateButtons = [
          {
            index: 0x1,
            [_button1]: {
              [_type1]: _template1[0x2],
              displayText: _template1[0x1],
            },
          },
          {
            index: 0x2,
            [_button2]: {
              [_type2]: _template2[0x2],
              displayText: _template2[0x1],
            },
          },
        ];
      _data["data"].forEach(({ number: _receiver, msg: _theTexts }) => {
        const _theTemplate = {
          text: _theTexts,
          footer: _data["footer"],
          templateButtons: _templateButtons,
        };
        (_result[_i] = { number: _receiver, msg: _theTemplate }), _i++;
      });
      break;
    default:
      break;
  }
  return _result;
}

function formatReceipt(_waId) {
  try {
    if (_waId.endsWith("@g.us")) return _waId;
    let _waIdType = _waId.replace(/\D/g, "");
    return (
      _waIdType.startsWith("0") && (_waIdType = "62" + _waIdType.substr(0x1)),
      !_waIdType.endsWith("@s.whatsapp.net") && (_waIdType += "@s.whatsapp.net"),
      _waIdType
    );
  } catch (_err) {
    console.log(_err);
  }
}

// function sleep(ms) {
//   return new Promise((resolve) => setTimeout(resolve, ms));
// }

const toSqlDateTime = (dateInput) => {
  const date = new Date(dateInput);
  const dateWithOffset = new Date(
    date.getTime() - date.getTimezoneOffset() * 60000
  );
  return dateWithOffset.toISOString().slice(0, 19).replace("T", " ");
};

function randomNumber(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function timeNow() {
  let _date = new Date();
  return _date.getHours() + ":" + _date.getMinutes() + ":" + _date.getSeconds();
}

async function fetchUrl (url, options) {
  try {
    options ? options : {};
    const res = await axios({
      method: "GET",
      url: url,
      headers: {
        "User-Agent":
          "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36",
      },
      ...options,
    });
    return res;
  } catch (err) {
    return err;
  }
};

async function fetchBuffer (url, options) {
	try {
		options ? options : {}
		const res = await axios({
			method: "GET",
			url,
			headers: {
                "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36",
				'DNT': 1,
				'Upgrade-Insecure-Request': 1
			},
			...options,
			responseType: 'arraybuffer'
		})
    return res;
	} catch (err) {
		return err
	}
}

module.exports = {
  formatSendBlast: formatSendBlast,
  getFileTypeFromUrl: getFileTypeFromUrl,
  formatReceipt: formatReceipt,
  // sleep: sleep,
  toSqlDateTime: toSqlDateTime,
  randomNumber: randomNumber,
  timeNow: timeNow,
  fetchUrl: fetchUrl,
  fetchBuffer: fetchBuffer,
};
