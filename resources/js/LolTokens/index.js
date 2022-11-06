// puppeteer-extra is a drop-in replacement for puppeteer,
// it augments the installed puppeteer with plugin functionality
const puppeteer = require('puppeteer-extra')

// add stealth plugin and use defaults (all evasion techniques)
const StealthPlugin = require('puppeteer-extra-plugin-stealth')
puppeteer.use(StealthPlugin())

async function sleep(ms) {

    return new Promise((resolve) => {
        setTimeout(resolve, ms);
    });
}

async function type(page, selector, text) {
    await page.waitForSelector(selector);
    await page.focus(selector);
    await page.keyboard.type(text, {delay: 75});
}

async function click(page, selector) {
    try{
        await page.waitForSelector(selector);
        //console.log('wait ', selector)
        await page.focus(selector);
        //console.log('focus ', selector)
        await page.click(selector, {duration: 500});
        //console.log('clicked ', selector)
        //console.log()
    }
    catch (e) {
        console.log(e);
    }
}


let username = null;
let password = null;

process.argv.forEach(function (val, index, array) {
    if (index === 2) {
        username = val;
    }
    if (index === 3) {
        password = val;
    }
});
if (username === null || password === null) {
    console.log("Please provide a username and password");
    process.exit(1);
}

// puppeteer usage as normal
puppeteer.launch({
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    headless: true
}).then(async browser => {
    const page = await browser.newPage()
    // Apply headers
// Referer: https://auth.riotgames.com/
// DNT: 1
// Connection: keep-alive

    await page.setExtraHTTPHeaders({
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
        'Accept-Encoding': 'gzip, deflate, br',
    });
    await page.goto('https://developer.riotgames.com/')
    await sleep(1000)

    let btnLoginSelector = ".navbar-avatar[role=\"button\"]"
    await click(page, btnLoginSelector)

    let usernameSelector = 'input[name="username"]'
    let passwordSelector = 'input[name="password"]'
    let btnSelector = 'button[type="submit"]'


    let acceptCookieSelector = ".osano-cm-accept-all";
    await click(page, acceptCookieSelector);

    await type(page, usernameSelector, username)
    await type(page, passwordSelector, password)
    await sleep(1000)
    await click(page, btnSelector)
    await sleep(1000)
    let btnRegenerateSelector = 'input[value="Regenerate API Key"]'
    let textExpiredSelector = 'b.riotred'
    let btnRegenerate = await page.waitForSelector(btnRegenerateSelector)
    let textExpired = false
    try{
        await page.$eval(textExpiredSelector, el => el.textContent)
        let textExpired = true
    }
    catch (e) {
    }
    if (textExpired) {
        let frameElement = await page.waitForSelector("iframe[title='reCAPTCHA']", {timeout: 10000})
        if (frameElement) {
            await sleep(2000)
            let frame =  await page.mainFrame().childFrames().find( async frame => {
                let title = await frame.title();
                return title === 'reCAPTCHA';
            })
            if (frame) {
                await click(frame, "#rc-anchor-container")
                await sleep(2000)
                // check style of .recaptcha-checkbox-border is display: none;
                let captchaOK = await frame.$eval(".recaptcha-checkbox-border", el => el.style.display) === 'none'
                if (captchaOK) {
                    //console.log('captcha OK')
                    await click(page, btnRegenerateSelector)
                }
                else{
                    //console.log('captcha not ok')
                    process.exit(1);
                }
            }
            else{
                //console.log('no frame')
                process.exit(1);
            }
        }
        await sleep(1000)
    }

    let apiKeySelector = 'input#apikey'
    await page.waitForSelector(apiKeySelector)
    let token = await page.$eval(apiKeySelector, el => el.value)
    console.log(token)
    await browser.close()

})