// http://eslint.org/docs/user-guide/configuring
module.exports = {
    root: true,
    parser: 'babel-eslint',
    env: {
        browser: true,
        node: true,
        es6: true,
    },
    globals: {
        React: true
    },
    parserOptions: {
        ecmaFeatures: {
            jsx: true
        }
    },

    // https://github.com/feross/standard/blob/master/RULES.md#javascript-standard-style
    extends: ["eslint:recommended", "plugin:react/recommended"],
    // required to lint *.vue files
    plugins: [
        'html', 'react'
    ],
    // add your custom rules here
    "ignorePatterns": ["/docs/*.js", "/nova/**", "/nova-components/**"],
    'rules': {
        // allow paren-less arrow functions
        'arrow-parens': 0,
        // allow async-await
        'generator-star-spacing': 0,

        // This should be removed
        "no-unused-vars": "off"
    }
}
