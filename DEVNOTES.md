DEVNOTES
========

### SQL

#### Obtenir tout les bookmarks : 

<pre><code>
select id,user_id,url,description,title,created_at,
GROUP_CONCAT(tags.tag) as tags
from bookmarks 
join tags on tags.bookmark_id = bookmarks.id 
group by bookmarks.id
</code></pre>

<pre><code>
http://wimg.ca

$file->move(dir,$file->getClientOriginalName);
$extension = $this->guessExtension();


$.ajax({url:"/json/bookmark",data:JSON.stringify({"title":"Google.com","description":"google website","url":"http://google.com","tags":["search","search engine","google"]}),success:function(){console.log(arguments);},contentType:"application/json",type:"POST"})



$.ajax({url:"/json/bookmark",data:JSON.stringify({"title":"yahoo.com","description":"yahoo website","url":"http://yahoo.com","tags":["directory","advertising","yahoo"]}),success:function(){console.log(arguments);},contentType:"application/json",type:"POST"})


SELECT id,url,title,description, created_at ,GROUP_CONCAT(tag,',')AS tags FROM bookmarks LEFT OUTER JOIN tags ON bookmarks.id = tags.bookmark_id WHERE  user_id = 1 GROUP BY id ORDER BY created_at DESC  LIMIT 0, 50 

angularjs.org

$.ajax({url:"/json/bookmark",data:JSON.stringify({"title":"angularjs.org","description":"angularjs javascript framework","url":"http://angularjs.org","tags":["angularjs","framework","library","mvc","javascript","google"]}),success:function(){console.log(arguments);},contentType:"application/json",type:"POST"})

https://github.com

$.ajax({url:"/json/bookmark",data:JSON.stringify({"title":"github","description":"github git repositories","url":"https://github.com","tags":["git","github","version management","open source"]}),success:function(){console.log(arguments);},contentType:"application/json",type:"POST"})

http://twitter.github.com/bootstrap

$.ajax({url:"/json/bookmark",data:JSON.stringify({"title":"Bootstrap","description":"front-end framework for faster and easier web development.","url":"http://twitter.github.com/bootstrap","tags":["git","github","twitter","bootstrap","css","framework","front end"]}),success:function(){console.log(arguments);},contentType:"application/json",type:"POST"})

</code></pre>
