# telegram-bot
A simple Telegram chatbot, markov-chain based

For it to work, you need to create bot using @BotFather on telegram. Once you have the token, you need to setup the webhook calling this link from a browser:

https://api.telegram.org/bot{yourTokenHere}/setWebhook?url={theUrlWhereYouPlaceYourBotHere}?token={yourTokenHere}

Call it just once, it should return ok.

Then, in that URL place a php file like the example.
