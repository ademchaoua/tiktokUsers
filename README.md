# TikTok Username Claimer Bot
- This Telegram bot allows users to claim TikTok usernames by interacting with TikTok's API. Built using PHP, the bot provides a streamlined interface for managing TikTok usernames, making it easy to claim desired usernames.

## Features
- **Fetch Current Username:** Retrieve the current TikTok username associated with a session.
- **Claim New Username:** Attempt to claim a new TikTok username.
- **Verify Username Change:** Check if the username has been successfully claimed and updated.

## How It Works

1. **Fetch Profile:** The bot first fetches the current TikTok profile using the session ID, device ID, and installation ID (iid).
2. **Username Claim:** If the desired username is available, the bot sends a request to claim it using the TikTok API.
3. **Verification:** After attempting to claim the username, the bot verifies whether the username has been successfully updated.

## Getting Started
### Prerequisites
- PHP 7.x or higher
- Telegram Bot Token: Obtain from BotFather
### Steps
1. Clone the Repository:
```bash
git clone https://github.com/ademchaoua/tiktokUsers.git
cd tiktokUsers
```
2. Set the Telegram Bot Token:

  Manually set your Telegram Bot Token in the following files:

  -  `setWebhook/webhook.php` on line 48
  -  `function/telegramBot.php` on line 4

3. Choose a Way to Run the Bot:

You can run the bot in one of the following ways:

- **Way 1:** Set the Telegram webhook for the `main.php` file.

- **Way 2:** Run `setWebhook/webhook.php` manually.
