const processing = async (sock, jid, data, options = {}) => {
  let type = data.type;
  try {
    switch (type) {
      case "contact":
        await sock.sendContact(jid, data.body.vcard, { ...options }).catch((err) => {
          throw new Error(err);
        });
        break;
      case 'text':
        await sock.sendMessage(jid, { text: data.body.text }, { ...options })
          .catch((err) => {
            throw new Error(err);
          });
        break;
      case 'media':
        await sock.sendMedia(jid, data.body, { ...options }).catch((err) => {
          console.log("error processing");
          throw new Error(err);
        });
        break;
      case 'button':
        await sock.sendButton(jid, data.body, { ...options }).catch((err) => {
          throw new Error(err);
        });
        break;
      case 'template':
        await sock.sendTemplate(jid, data.body, { ...options }).catch((err) => {
          throw new Error(err);
        });
        break;
      case 'location':
        await sock.sendLocation(jid, data.body, { ...options }).catch((err) => {
          throw new Error(err);
        });
        break;
      default:
        await sock.sendMessage(jid, data.body, { ...options }).catch((err) => {
          throw new Error(err);
        });
        break;
    }
  } catch (error) {
    console.log( error);
    throw new Error(error);
  }
};

module.exports = {
  processing: processing,
};
