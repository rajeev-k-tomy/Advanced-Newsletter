Rkt_NLforGuest is a Magento extension that is used for better management of newsletter in Magento. This extension will provide better control over newsletter so that both subscriber and newsletter provider can get better satisfaction.

What is the Importance of Rkt_NLforGuest
-------------------------------------------

The default behavior of Magento is like this. if the guest subscription is not allowed, then guest users cannot subscribe newsletters. If it is enabled and confirmation is not requered, then for guest and logged in users, Magento will send success mail. If confirmation is set to yes, then for logged in user, it will send success mail(he/she doesn't want to confirm. A Valid customer's email address is always valid.) and for guest users, it will send a confirmation letter. In order to complete the subscription, guest users need to confirm it by clicking on the link that Magento sends to the provided email address. 

I feel the default behavior of Magento is not good enough. Since Magento does not provide any control over success mail. This extension fills this gap. After the installation of this extension, Magento will behave like this.


That is this extension adds a new newsletter configuration field `allow success mail for guest`. Due to this, there are 8 unique conditions occurs. But it will provide us better control. For the sake of understanding, I will try to explain it in short.

- **For guest users**, newsletter facility is enabled only when `allow guest subscription` is set to yes. if this condition is met, then it will check whether `confirmation` is enabled or not. If not, it will then check success mail is allowed. If yes, then it will send success mail. Otherwise, it will not.
 
- If `confirmation` is enabled then it will send a confirmation mail to the guest. When they try to confirm, it will again check the status of success mail, if it is not allowed, then the extension will redirect login/registration page and force them to create an account. If guest creates a new account or log in, it will allow newsletter subscription to that guest by sending the success mail. If he didn't log in, then success mail will not send. 

- If success mail is allowed, then it will send success mail to guest and grand newsletter subscription.

- **For logged in users**, if confirmation is enabled, then it will send confirmation mail. When they try to confirm, the extension will send success mail to those users. If confirmation is not allowed, it will directly send a confirmation mail to those users. But if the newsletter email address and logged in customer email address do not match, then the extension will not allow subscribing newsletter unless it subscription is allowed for guest users.

Pros & Cons
------------

It allows better management of newsletters.

Admin can control all conditions in the newsletter lifetime.

It involves a controller rewrite

Version Support
----------------

I have tested this extension only in Magento 1.8. But I guess, it will work with other versions also.

Installation
-------------

Download the zip

Unzip it

Go to root directory of Magento and paste it there.

NOTE: I will make it available in Magento Connect soon.

                    
