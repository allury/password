# 安全密码生成器

一个功能完整、安全可靠的密码生成工具，完全包含在一个 PHP 文件中。

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php)
![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Version](https://img.shields.io/badge/version-2.0.0-green.svg)

## 在线演示

👉 [立即体验](https://tool.706632.xyz/passwd.php)

## 主要功能

- **加密安全生成**：使用 `random_int()` 生成高强度随机密码
- **智能过滤**：默认过滤易混淆字符（`0O1lI`），并避免连续重复字符
- **强制复杂度**：确保每种选中的字符类型至少出现一次
- **实时交互**：修改任意选项后自动重新生成密码
- **密码强度评估**：实时可视化显示密码强度
- **一键复制**：支持快速复制生成的密码
- **主题切换**：支持浅色 / 深色主题，并自动保存用户偏好
- **优雅降级**：即使禁用 JavaScript 也能正常使用

## 快速开始

由于是单文件应用，部署非常简单：

1. 下载 `passwd.php` 文件
2. 上传到支持 PHP 的服务器（Apache / Nginx / Caddy 等）
3. 直接通过浏览器访问该文件即可使用

**无需安装、无需配置数据库、无需额外依赖。**

## 功能说明

### 后端特性
- 使用 `random_int()` 生成加密安全随机数
- 默认开启「排除相似字符」功能
- 默认开启「避免连续重复字符」功能
- 自动保证字符类型多样性
- 使用 Fisher-Yates 算法进行最终打乱

### 前端特性
- 现代化响应式界面
- 支持浅色与深色主题切换（localStorage 持久化）
- 实时 AJAX 生成，无需刷新页面
- 实时密码强度指示器
- 完善的 JavaScript 降级支持

## 技术栈

- **后端**：纯 PHP（无任何框架或外部依赖）
- **前端**：原生 HTML + CSS + JavaScript（ES6+）
- **样式**：CSS 变量 + 响应式设计
- **安全**：输出使用 `htmlspecialchars()` 编码

## 许可证

本项目基于 [MIT License](LICENSE) 开源协议。
