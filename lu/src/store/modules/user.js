import Cookies from 'js-cookie';

import * as types from '../mutation-types';

import Util from '../../libs/util';

const user = {
    state: {
        access_token: null,
        access_token_type: null,
        current_user: null
    },
    actions: {
        login({commit}, userForm) {
            return new Promise(function (resolve, reject) {
                if (localStorage.refresh_token) {
                    Util.ajax.post('/refreshtoken', {
                        refresh_token: localStorage.refresh_token
                    }).then(function (response) {
                        let response_data = response.data;
                        commit(types.LOGIN_SUCCESS, response_data.data);
                        resolve(response_data.data);
                    }, function (error) {
                        commit('logout');
                        reject(error);
                    })

                } else {
                    Util.ajax.post('/login', {
                        email: userForm.email,
                        password: userForm.password
                    }).then(function (response) {
                        let response_data = response.data;
                        commit(types.LOGIN_SUCCESS, response_data.data);
                        resolve(response_data.data);
                    }, function (error) {
                        commit('logout');
                        reject(error);
                    })
                }

            })

        },
    },
    mutations: {
        [types.LOGIN_SUCCESS](state, data) {
            localStorage.refresh_token = data.token.refresh_token;
            localStorage.access_token = data.token.access_token;
            localStorage.access_token_type = data.token.token_type;
            localStorage.current_user = JSON.stringify(data.user);
            localStorage.current_user_roles = JSON.stringify(data.user.roles);
            Cookies.set('current_roles', JSON.stringify(data.user.roles, {expires: 1}));
            Cookies.set('refresh_token', data.token.refresh_token, {expires: 1});
            Cookies.set('access_token', data.token.access_token, {expires: 1});
        },
        logout(state, vm) {
            // 恢复默认样式
            let themeLink = document.querySelector('link[name="theme"]');
            themeLink.setAttribute('href', '');
            // 清空打开的页面等数据，但是保存主题数据
            let theme = '';
            if (localStorage.theme) {
                theme = localStorage.theme;
            }
            localStorage.clear();
            Cookies.remove('current_roles');
            Cookies.remove('refresh_token');
            Cookies.remove('access_token');
            if (theme) {
                localStorage.theme = theme;
            }
        }
    }
};

export default user;
