import Main from '@/views/Main.vue';

// 不作为Main组件的子页面展示的页面单独写，如下
export const loginRouter = {
    path: '/login',
    name: 'login',
    meta: {
        title: 'Login - 登录'
    },
    component: () => import('@/views/login.vue')
};

export const page404 = {
    path: '/*',
    name: 'error-404',
    meta: {
        title: '404-页面不存在'
    },
    component: () => import('@/views/error-page/404.vue')
};

export const page403 = {
    path: '/403',
    meta: {
        title: '403-权限不足'
    },
    name: 'error-403',
    component: () => import('@//views/error-page/403.vue')
};

export const page500 = {
    path: '/500',
    meta: {
        title: '500-服务端错误'
    },
    name: 'error-500',
    component: () => import('@/views/error-page/500.vue')
};



export const locking = {
    path: '/locking',
    name: 'locking',
    component: () => import('@/views/main-components/lockscreen/components/locking-page.vue')
};

// 作为Main组件的子页面展示但是不在左侧菜单显示的路由写在otherRouter里
export const otherRouter = {
    path: '/',
    name: 'otherRouter',
    redirect: '/home',
    component: Main,
    children: [
        {path: 'home', title: {i18n: 'home'}, name: 'home_index', component: () => import('@/views/home/home.vue')},
        {
            path: 'privileges/user/users-add',
            title: '添加用户',
            name: 'add-user',
            component: () => import('@/views/privileges/users/add-user.vue')
        },
        {
            path: 'privileges/user/:user_id',
            title: '编辑用户',
            name: 'edit-user',
            component: () => import('@/views/privileges/users/edit-user.vue')
        },
        {
            path: 'news-system/advertisements-add',
            title: '添加广告',
            name: 'add-advertisement',
            component: () => import('@/views/news-system/advertisements/add-advertisement.vue')
        },
        {
            path: 'news-system/advertisements-edit',
            title: '编辑广告',
            name: 'edit-advertisement',
            component: () => import('@/views/news-system/advertisements/edit-advertisement.vue')
        },
        {
            path: 'articles/articles-add',
            title: '添加文章',
            name: 'add-article',
            component: () => import('@/views/news-system/articles/add-article.vue')
        },
        {
            path: 'articles/articles-edit',
            title: '编辑文章',
            name: 'edit-article',
            component: () => import('@/views/news-system/articles/edit-article.vue')
        },
        {
            path: 'ownspace',
            title: '个人中心',
            name: 'ownspace_index',
            component: () => import('@/views/own-space/own-space.vue')
        },
        {path: 'message', title: '消息中心', name: 'message_index', component: () => import('@/views/message/message.vue')}
    ]
};

// 作为Main组件的子页面展示并且在左侧菜单显示的路由写在appRouter里
export const appRouter = [
    {
        path: '/privileges',
        icon: 'key',
        name: 'access',
        title: '权限管理',
        component: Main,
        access: ['Founder'],
        children: [
            {
                path: 'permission-list',
                icon: 'ios-locked',
                name: 'permission-list',
                title: '权限列表',
                component: () => import('@/views/privileges/permissions/list.vue')
            },
            {
                path: 'role-list',
                icon: 'ios-people',
                name: 'role-list',
                title: '角色列表',
                component: () => import('@/views/privileges/roles/list.vue')
            },
            {
                path: 'administrator-list',
                icon: 'person-stalker',
                name: 'administrator-list',
                title: '用户列表',
                component: () => import('@/views/privileges/users/list.vue')
            },
        ]
    },
    {
        path: '/news-system',
        icon: 'ios-cog',
        title: '新闻系统',
        name: 'news-system',
        component: Main,
        access: ['Founder', 'news_listor'],
        children: [
            {
                path: 'advertisement-positions',
                icon: 'speakerphone',
                title: '广告位',
                name: 'advertisement-positions',
                component: () => import('@/views/news-system/advertisement-positions/list.vue')
            },
            {
                path: 'advertisement-list',
                icon: 'volume-high',
                title: '广告列表',
                name: 'advertisement-list',
                component: () => import('@/views/news-system/advertisements/list.vue')
            },
            {
                path: 'category-list',
                icon: 'navicon',
                title: '分类管理',
                name: 'category-list',
                component: () => import('@/views/news-system/categories/list.vue')
            },
            {
                path: 'tag-list',
                icon: 'ios-pricetags',
                title: '标签管理',
                name: 'tag-list',
                component: () => import('@/views/news-system/tags/list.vue')
            },
            {
                path: 'article-list',
                icon: 'ios-list-outline',
                title: '文章管理',
                name: 'article-list',
                component: () => import('@/views/news-system/articles/list.vue')
            },
        ]
    },
    {
        path: '/resources',
        icon: 'link',
        title: '资源管理',
        name: 'resources',
        component: Main,
        children: [
            {
                path: 'attachments',
                icon: 'link',
                title: '附件列表',
                name: 'attachments',
                component: () => import('@/views/resources/attachments/list.vue')
            }
        ]
    },
    {
        path: '/security',
        icon: 'link',
        title: '安全管理',
        name: 'security',
        component: Main,
        children: [
            {
                path: 'system-logs',
                icon: 'link',
                title: '系统日志',
                name: 'system-logs',
                component: () => import('@/views/security/logs/list.vue')
            },
            {
                path: 'ip-filters',
                icon: 'alert',
                title: 'ip 过滤',
                name: 'ip-filters',
                component: () => import('@/views/security/ip_filters/list.vue')
            }
        ]
    },
    {
        path: '/component',
        icon: 'social-buffer',
        name: 'component',
        title: '组件',
        component: Main,
        children: [
            {
                path: 'scroll-bar',
                icon: 'android-upload',
                name: 'scroll-bar',
                title: '滚动条',
                component: () => import('@/views/my-components/scroll-bar/scroll-bar-page.vue')
            },
        ]
    },
    {
        path: '/error-page',
        icon: 'android-sad',
        title: '错误页面',
        name: 'errorpage',
        component: Main,
        children: [
            {
                path: 'index',
                title: '错误页面',
                name: 'errorpage_index',
                component: () => import('@/views/error-page/error-page.vue')
            }
        ]
    }
];

// 所有上面定义的路由都要写在下面的routers里
export const routers = [
    loginRouter,
    otherRouter,
    locking,
    ...appRouter,
    page500,
    page403,
    page404
];
