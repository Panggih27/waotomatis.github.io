
const { sendMessageJob } = require('./SendMessageJob')
const { body } = require('express-validator');
const { sendMessageBC2 } = require("./BroadcastMessage2");
const { fetchAllGroup, getGroupMetaData } = require("./Group");
const { TestMessage } = require("./TestMessage");
module.exports = function (router) {

    router.post('/broadcast-delay2', [
        body('campaign', 'Wrong Parameters!').notEmpty(),
    ], sendMessageBC2)

    router.post('/send-message-by-job', [
        body('sender', 'Wrong Parameters!').notEmpty(),
        body('receiver', 'Wrong Parameters!').notEmpty(),
        body('type', 'Wrong Parameters!').notEmpty(),
        body('body', 'Wrong Parameters!').notEmpty(),
    ], sendMessageJob)

    router.post('/send-message-test', [
        body('sender', 'Wrong Parameters!').notEmpty(),
        body('receiver', 'Wrong Parameters!').notEmpty(),
        body('type', 'Wrong Parameters!').notEmpty(),
        body('body', 'Wrong Parameters!').notEmpty(),
    ], TestMessage)

    router.get("/fetch-all-group/:sender", fetchAllGroup);
    router.get("/metadata-group/:sender/:jid", getGroupMetaData);

}
