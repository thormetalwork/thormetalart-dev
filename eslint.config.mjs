import js from "@eslint/js";
import globals from "globals";

export default [
  {
    ignores: ["**/*.min.js", "**/node_modules/**", "**/vendor/**"],
  },
  js.configs.recommended,
  {
    files: ["data/wordpress/wp-content/plugins/tma-panel/assets/js/**/*.js"],
    languageOptions: {
      ecmaVersion: 2021,
      sourceType: "script",
      globals: {
        ...globals.browser,
        Chart: "readonly",
        wp: "readonly",
      },
    },
    rules: {
      "no-unused-vars": "warn",
      "no-console": "off",
      "eqeqeq": "warn",
      "no-var": "warn",
      "prefer-const": "warn",
    },
  },
];
