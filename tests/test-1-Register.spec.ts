import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('http://localhost/CA4_game/menu.php');
  await page.getByRole('link', { name: 'Register' }).click();
  await page.locator('input[name="username"]').click();
  await page.locator('input[name="username"]').fill('A');
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="password"]').fill('a');
  await page.getByRole('button', { name: 'Register' }).click();
});