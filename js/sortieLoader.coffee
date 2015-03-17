url = 'http://www.gestdown.info/api/sorties.php'
nb = 5
openNewTab = (tabUrl) ->
  win = window.open(tabUrl, '_blank');
  win.focus()

$ ->
  releaseDiv = $ "#release"
  url += '?nb=' + nb
  imgur = '//i.imgur.com/'
  $.getJSON url, (data)->
    ul = $("<ul>").appendTo(releaseDiv)
    for elem in data
      console.log elem
      episode = elem.episode
      serie = elem.serie
      screen = elem.screen
      epInfo = serie + '<br />' + episode
      title = serie + ' - ' + episode
      href = "http://www.gestdown.info/ep-" + elem.id + ".html"
      screenPart = screen.split "?"
      screenPart = screenPart[0].split('/').pop()
      screenPart = screenPart.split "."
      screen = imgur + screenPart[0] + 'm.' + screenPart[1]
      li = $ "<li>"
      .appendTo ul
      div = $ '<div>'
      .addClass 'episodeInfo'
      .html epInfo
      .appendTo li
      .click ->
        __gaTracker('send', 'event', 'sortie', href, title);
        openNewTab(href)
      img = $ "<img>"
      .attr
          src: screen
          alt: title
          class: 'sortie'

      a = $ '<a>'
      .attr
          href: href
          target: '_blank'
      .html img
      .appendTo li
      .click ->
        __gaTracker('send', 'event', 'sortie', href, title);
      img