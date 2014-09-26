Rkt_NLforGuest is a Magento extension that is used for better management of newsletter in Magento. This extension will provide better control over newsletter, so that both subscriber and newsletter provider can get better satisfaction.

What is the Importance of Rkt_NLforGuest
-------------------------------------------

Magento's default newsletter behaviour is as shown below.

--------------------------------------------------------------------------------------------------
|Need To Confirm    |    Allow Guest Subscription    |                Result                     |
--------------------------------------------------------------------------------------------------
|                   |                                |LOGGED IN : Send success Mail              |
|       NO          |            NO                  |-------------------------------------------|
|                   |                                |GUEST : Not allow subscription             |
-------------------------------------------------------------------------------------------------|
|                   |                                |LOGGED IN : Send Success Mail              |
|                   |                                |-------------------------------------------|
|       NO          |            YES                 |GUEST : Send Success Mail                  |
-------------------------------------------------------------------------------------------------|
|                   |                                |LOGGED IN : Send Success Mail              |
|       YES         |            NO                  |-------------------------------------------|
|                   |                                |GUEST : Not allow subscriptoin             |
-------------------------------------------------------------------------------------------------|
|                   |                                |LOGGED IN : Send Success Mail              |
|       YES         |            YES                 |-------------------------------------------|
|                   |                                |GUEST : Send confirmation mail             |
-------------------------------------------------------------------------------------------------|

The above table describes different states of configuration of newsletter. In short, if guest subscription is not allowed, then guest users cannot subscribe newsletters. If it is enabled and confirmation is not requered, then for guest and logged in users, magento will send success mail. If confiramation is set to yes, then for logged in user, it will send success mail(he/she doesn't want to confirm. A Valid customer's email address is always valid.) and for guest users it will send confirmation letter. In order to complete subscription, then guest users need to confirm it by clicking on the link that Magento send to the provided email address. 

I feel the default behaviour of magento is good enough. Since Magento does not provide any control over success mail. This extension fills this gap. After the installation of this extension, Magento will somewhat behaves like this.

--------------------------------------------------------------------------------------------------------------------------------------
|Need To Confirm    |    Allow Guest Subscription    |    Allow Success Mail to Guest    |                Result                     |
-------------------------------------------------------------------------------------------------------------------------------------|
|                   |                                |                                   |LOGGED IN : Will not send success mail     |
|       NO          |            NO                  |                NO                 |-------------------------------------------|
|                   |                                |                                   |GUEST : Not allow subscription             |
|-------------------|--------------------------------|-----------------------------------|-------------------------------------------|
|                   |                                |                                   |LOGGED IN : Send Success Mail              |
|                   |                                |               YES                 |-------------------------------------------|
|       NO          |            NO                  |                                   |GUEST : Not allow subscription             |
|-------------------|--------------------------------|-----------------------------------|-------------------------------------------|
|                   |                                |                                   |LOGGED IN : Will not send success mail     |
|       NO          |            YES                 |                NO                 |-------------------------------------------|
|                   |                                |                                   |GUEST : Will not send success mail         |
|-------------------|--------------------------------|-----------------------------------|-------------------------------------------|
|                   |                                |                                   |LOGGED IN : Send Success Mail              |
|       NO          |            YES                 |               YES                 |-------------------------------------------|
|                   |                                |                                   |GUEST : Send success mail                  |
|-------------------|--------------------------------|-----------------------------------|-------------------------------------------|
|                   |                                |                                   |LOGGED IN : Send confirmation Mail         |
|                   |                                |                                   |            will not send success mail     |
|      YES          |            NO                  |                NO                 |-------------------------------------------|
|                   |                                |                                   |GUEST : Not allow subscription             |
--------------------------------------------------------------------------------------------------------------------------------------
|                   |                                |                                   |LOGGED IN : Send confirmation Mail         |
|                   |                                |                                   |            send success mail              |
|       YES         |            NO                  |               YES                 |-------------------------------------------|
|                   |                                |                                   |GUEST : Not allow subscription             |
|-------------------|--------------------------------|-----------------------------------|-------------------------------------------|
|                   |                                |                                   |LOGGED IN : Send confirmation mail         |
|                   |                                |                                   |            will not send success mail     |
|       YES         |            YES                 |                NO                 |-------------------------------------------|
|                   |                                |                                   |GUEST : Send confirmation mail             |
|                   |                                |                                   |        Will send success mail             |
--------------------------------------------------------------------------------------------------------------------------------------
|                   |                                |                                   |LOGGED IN : Send confirmation mail         |
|                   |                                |                                   |            send success mail              |
|       YES         |            YES                 |               YES                 |-------------------------------------------|
|                   |                                |                                   |GUEST : Send confirmation mail             |
|                   |                                |                                   |            send success mail              |
--------------------------------------------------------------------------------------------------------------------------------------

That is this extension adds a new newsletter configuration field `allow success mail for guest`. Due to this there are 8 unique conditions occurs. But it will provide us better control. For the sake of understanding, I will try to explain it in short

For guest users, newsletter facility is enabled only when `allow guest subscription` is set to yes. if this condition is met, then it will check whether `confirmation` is enabled or not. If not, it will then check success mail is allowed. if yes it will send success mail. Otherwise it will not. If `confirmation` is enabled then it will send confirmation mail to guest. When they try to confirm, it will again check status of success mail, if it is not allowed, then extension will redirect login/registration page and force them to create an account. If guest creates a new account or login, it will allow newsletter subscription to that guest by sending the success mail. If he didn't login, then success mail will not send. if success mail is allowed, then it will send success mail to guest and grand newsletter subscription.

For logged in user, if confirmation is enabled, then it will send confirmation mail. When they try to confirm, extension will send success mail to those users. If confirmation is not allowed, it will directly send confirmation mail to those users. But if the newsletter email address and logged in customer email address do not match, then extension will not allow to subscribe newsletter unless it subscription is allowed for guest users.

                    
