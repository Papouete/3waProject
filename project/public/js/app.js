// /*
//  * Welcome to your app's main JavaScript file!
//  *
//  * We recommend including the built version of this JavaScript file
//  * (and its CSS file) in your base layout (base.html.twig).
//  */

// // any CSS you import will output into a single css file (app.css in this case)
// // import './styles/app.css';
// // import 'tw-elements'

// let searchInput;
// let searchResults;

// function goSearch(e)
//   {
//     let search = e.target.value;
  
//     fetch(`/fetch/${search}`, {method: "GET"})
//       .then( response => response.json())
//       .then( results => {
//         searchResults.innerHTML = '';
//         if (results !== 'empty') {
//           for(let i=0; i<results.length; i++) {
//             displayArticle(results[i])
//           }
//           // results.forEach( result => displayArticle(result))
//         } else {
//           let p = document.createElement('p');
//           p.innerText = 'aucun resultat de la recherche';
//           searchResults.append(p)
//         }
//       });
//   }
  
//   function displayArticle(article)
//   { 
//     let domArticle = document.createElement('article');
    
//     let image = article.image ? `<img class="rounded-t-lg w-full" src="static/images/${article.image}" alt="${article.title}">` : "";
//     domArticle.innerHTML = `
//       <div class="max-w-sm bg-white rounded-lg border border-gray-200 shadow-md dark:bg-bg-orange-100 dark:border-gray-700" data-type="post">
//         ${image}
//         <div class="p-5">
//           <a href="/article/${article.slug}">
//             <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">${article.title }</h5>
//           </a>
//           <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">${article.content}</p>
// 	    </div>
//     </div>
//     `;
//     searchResults.append(domArticle);
//   }

// document.addEventListener('DOMContentLoaded', () => {
//   console.log('coucou');
//   console.log('Webpack Encore is working !');

//   searchInput = document.querySelector('#search_q');
//   searchResults = document.querySelector('#searchView');
//   searchInput = addEventListener('change', goSearch);

//   // Like's system
//   // const likeElements = [].slice.call(document.querySelectorAll('a[data-action="like"]'));
//   // if (likeElements) {
//   //   new Like(likeElements);
//   // };

// })
