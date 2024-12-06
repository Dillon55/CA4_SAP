import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('http://localhost/CA4_game_insecure/');
  await page.getByRole('link', { name: 'menu.php' }).click();
  await page.getByRole('link', { name: 'Login' }).click();
  await page.locator('input[name="username"]').click();
  await page.locator('input[name="username"]').fill('D');
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="username"]').click();
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="password"]').fill('p');
  await page.getByRole('button', { name: 'Login' }).click();
  await page.locator('input[name="username"]').click();
  await page.locator('input[name="username"]').fill('D');
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="password"]').fill('p');
  await page.getByRole('button', { name: 'Login' }).click();
  await page.getByRole('link', { name: 'Add Review' }).click();
  await page.locator('input[name="game_name"]').click();
  await page.locator('input[name="game_name"]').fill('r');
  await page.locator('textarea[name="review"]').click();
  await page.locator('textarea[name="review"]').fill('h');
  await page.getByLabel('Yes').check();
  await page.getByRole('button', { name: 'Submit Review' }).click();
});