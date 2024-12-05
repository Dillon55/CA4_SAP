import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('http://localhost/CA4_game/menu.php');
  await page.getByRole('link', { name: 'Login' }).click();
  await page.locator('input[name="username"]').click();
  await page.locator('input[name="username"]').fill('D');
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="password"]').fill('p');
  await page.getByRole('button', { name: 'Login' }).click();
  await page.getByRole('link', { name: 'Add Review' }).click();
  await page.locator('input[name="game_name"]').click();
  await page.locator('input[name="game_name"]').fill('g');
  await page.locator('textarea[name="review"]').click();
  await page.locator('textarea[name="review"]').fill('g');
  await page.getByText('Yes').click();
  await page.getByRole('button', { name: 'Submit Review' }).click();
});