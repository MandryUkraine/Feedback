<?xml version="1.0" encoding="UTF-8" ?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" indent="yes" encoding="UTF-8"  media-type="text/html" />

    <xsl:template match="/*">
        <xsl:text disable-output-escaping="yes">&lt;!DOCTYPE html></xsl:text>
        <html xml:lang="uk" lang="uk" dir="ltr" id="root">
            <xsl:if test="(@login=1)"><xsl:attribute name="data-login">true</xsl:attribute></xsl:if>
            <xsl:if test="(@debug=1)"><xsl:attribute name="data-debug">true</xsl:attribute></xsl:if>
            <head>
                <title><xsl:value-of select="@title" /></title>
                <meta name="description" content="{@description}" />
                <meta name="keywords" content="{@keywords}" />
                <meta property="og:url" content="{@url}" />
                <meta property="og:title" content="{@title}" />
                <meta property="og:description" content="{@description}" />
                <meta property="og:locale" content="uk_UA" />
                <meta name="viewport" content="width=device-width,initial-scale=1.0" />
                <meta name="robots" content="index, follow" />
                <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=1" />
                <link rel="stylesheet" type="text/css" href="/index.css?v=2" />
                <link rel="stylesheet" type="text/css" href="/bootstrap.min.css" />
                <script src="/jquery-3.2.1.min.js" />
                <script src="/index.js?v=2" />
                <xsl:if test="@login=0">
                    <script src='https://www.google.com/recaptcha/api.js?hl=uk' async="async" defer="defer" />
                </xsl:if>
                <xsl:if test="@login=1"><script src="/admin.js?v=1" /></xsl:if>
            </head>
            <body>
                <header>
                    <div class="logo">
                        <a href="/" title="{parameters/@title}"><xsl:value-of select="@logo" /></a>
                    </div>
                    <xsl:if test="@login=1">
                        <div class="exit">
                            <a href="/вихід" title="Вихід з режиму редагування">Вихід</a>
                        </div>
                    </xsl:if>
                    <div class="clear" />
                </header>
                <main class="{name(main/*[1])}">
                    <div class="top">
                        <div class="title cell">
                            <h1>
                                <xsl:value-of select="main/@title" />
                                <xsl:if test="main/@subtitle">
                                    &#160;(<xsl:value-of select="main/@subtitle" />)
                                </xsl:if>
                            </h1>
                        </div>
                        <xsl:if test="main/order">
                            <div class="order cell">
                                <select name="order" title="Сортування (порядок)">
                                    <xsl:for-each select="main/order/item">
                                        <option value="{@value}">
                                            <xsl:if test="@selected">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>
                                            <xsl:value-of select="@name" />
                                        </option>
                                    </xsl:for-each>
                                </select>
                            </div>
                        </xsl:if>
                        <div class="clear" />
                    </div>
                    <xsl:if test="@message!=''">
                        <div id="message" class="block message"><p><xsl:value-of select="@message" /></p></div>
                    </xsl:if>
                    <xsl:apply-templates select="main/*[1]" />
                </main>
                <footer>
                    <div class="copyright"><xsl:value-of select="@copyright" /></div>
                </footer>
                <xsl:if test="@debug=1"><xsl:apply-templates select="debug" /></xsl:if>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="list">
        <xsl:choose>
            <xsl:when test="messages">
                <div class="messages">
                    <xsl:for-each select="messages/message">
                        <div class="message" id="message{@id}">
                            <div class="header">
                                <xsl:if test="/root/@login">
                                    <xsl:attribute name="data-id"><xsl:value-of select="@id" /></xsl:attribute>
                                </xsl:if>
                                <div class="date"><xsl:value-of select="@date" /></div>
                                <div class="name"><xsl:value-of select="@name" /></div>
                                <div class="email"><xsl:value-of select="@email" /></div>
                                <div class="site"><xsl:value-of select="@site" /></div>
                                <xsl:if test="/root/@login=1">
                                    <div class="control">
                                        <a href="/edit/{@id}" class="edit" />
                                        <a href="/delete/{@id}" class="delete" />
                                    </div>
                                </xsl:if>
                            </div>
                            <div class="text"><xsl:value-of select="@text" /></div>
                            <xsl:if test="/root/@login=1">
                                <div class="footer">
                                    <div class="ip"><xsl:value-of select="@ip" /></div>
                                    <div class="browser"><xsl:value-of select="@browser" /></div>
                                </div>
                            </xsl:if>
                        </div>
                    </xsl:for-each>
                </div>
                <xsl:if test="pagination">
                    <div class="pagination">
                        <xsl:for-each select="pagination/page">
                            <a href="{@uri}" title="{@title}">
                                <xsl:if test="@active">
                                    <xsl:attribute name="class">active</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="@value" />
                            </a>
                        </xsl:for-each>
                    </div>
                </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <p class="empty">Повідомлення відсутні. Ви будете першим!</p>
            </xsl:otherwise>
        </xsl:choose>
        <div class="form">
            <xsl:if test="/root/@login=1">
                <xsl:attribute name="class">form hidden</xsl:attribute>
            </xsl:if>
            <form action="/" method="POST">
                <div class="field name">
                    <input type="text" name="name" placeholder="Ім'я" required="required"
                           title="Кирилиця (від 3 до 64 символів)" />
                </div>
                <div class="field email">
                    <input type="text" name="email" placeholder="Адреса електронної пошти" required="required"
                           title="Латиниця, цифри та дозволені символи (від 7 до 129 символів)" />
                </div>
                <div class="field site">
                    <input type="text" name="site" placeholder="Адреса сайту"
                           title="Любі дозволені символи крім пробілу (від 7 до 129 символів)"/>
                </div>
                <div class="field text">
                    <textarea name="text" placeholder="Текст повідомлення" required="required"
                              title="Любі символи (від 3 до 512 символів)">
                    </textarea>
                </div>
                <xsl:if test="/root/@login=0">
                    <div class="captcha">
                        <div class="g-recaptcha" data-sitekey="{/root/@recaptcha}" />
                    </div>
                </xsl:if>
                <div class="buttons">
                    <input type="submit" name="submit" value="Зберегти" />
                    <input type="reset" value="Очистити" />
                    <xsl:if test="/root/@login=1">
                        <input type="hidden" name="id" />
                        <input type="hidden" name="date" />
                    </xsl:if>
                </div>
            </form>
        </div>
    </xsl:template>

    <xsl:template match="login">
        <div class="form">
            <form method="POST">
                <div class="login field">
                    <input type="text" name="login" pattern="[a-z0-9]{{3,16}}" placeholder="Логін" />
                </div>
                <div class="password field">
                    <input type="password" name="password" pattern="[a-zA-Z0-9]{{3,16}}" placeholder="Пароль" />
                </div>
                <div class="captcha">
                    <div class="g-recaptcha" data-sitekey="6LeFxTsUAAAAAAY143DBpdaZfEZl6bMsjkI_6HfD" />
                </div>
                <div class="buttons">
                    <input type="submit" name="submit" value="Відправити" />
                    <input type="reset" value="Очистити" />
                </div>
            </form>
        </div>
    </xsl:template>

    <xsl:template match="test">
        <div id="test"/>
    </xsl:template>

    <xsl:template match="pageNotFound">
        <div id="pageNotFound">
            <h2><xsl:value-of select="/root/@description" /></h2>
            <p>Page Not Found (Text)</p>
        </div>
    </xsl:template>

    <xsl:template match="debug">
        <div id="debug">
            <h2 class="title">Відлагодження</h2>
            <div class="common">
                <div class="row time">
                    <div class="title">Загальний час, мс</div>
                    <div class="value"><xsl:value-of select="@time" /></div>
                </div>
                <xsl:if test="mapper/@time">
                    <div class="row mapper-time">
                        <div class="title">Час витрачений на Mapper, мс</div>
                        <div class="value"><xsl:value-of select="mapper/@time" /></div>
                    </div>
                </xsl:if>
                <div class="row memory">
                    <div class="title">Використано пам’яті, kB</div>
                    <div class="value"><xsl:value-of select="@memory" /></div>
                </div>
                <div class="row peak-memory">
                    <div class="title">Максимальний рівень пам’яті, kB</div>
                    <div class="value"><xsl:value-of select="@memoryPeak" /></div>
                </div>
            </div>
            <xsl:if test="mapper">
                <div class="mapper">
                    <xsl:for-each select="mapper/queries/query">
                        <div class="query">
                            <div class="position"><xsl:value-of select="position()" /></div>
                            <div class="time"><span><xsl:value-of select="@time" /></span></div>
                            <div class="sql"><pre><xsl:value-of select="@sql" disable-output-escaping="yes" /></pre></div>
                        </div>
                    </xsl:for-each>
                </div>
            </xsl:if>
            <xsl:if test="trace/@message!=''">
                <h4 class="exception"><span>Виняток: </span><xsl:value-of select="trace/@message" /></h4>
                <div class="trace">
                    <xsl:for-each select="trace/item">
                        <div class="item">
                            <div class="position"><xsl:value-of select="position()" />.</div>
                            <div class="file"><span><xsl:value-of select="@file" /></span></div>
                            <div class="line"><span><xsl:value-of select="@line" /></span></div>
                            <div class="function"><span><xsl:value-of select="@function" /></span></div>
                            <div class="arg"><xsl:value-of select="arg" disable-output-escaping="yes" /></div>
                        </div>
                    </xsl:for-each>
                </div>
            </xsl:if>
        </div>
    </xsl:template>


    <xsl:template match="pagination">
        <div class="pagination">
            <xsl:for-each select="pages/page">
                <a href="{@alias}" title="{@title}">
                    <xsl:if test="@active">
                        <xsl:attribute name="class">active</xsl:attribute>
                    </xsl:if>
                    <xsl:value-of select="@value" />
                </a>
            </xsl:for-each>
        </div>
    </xsl:template>


</xsl:stylesheet>