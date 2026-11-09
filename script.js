const products = {
  dragao: {name:"Dragão articulado", price:79.90, category:"GEEK / IMPRESSO EM 3D", description:"20 cm de pura personalidade. Um dragão articulado, impresso em 3D, para trazer um pouco de fantasia ao seu setup ou à sua coleção.", image:"assets/dragao-detalhe.png", alt:"Dragão articulado laranja"},
  astro: {name:"Astro planter", price:69.90, category:"DECOR / 12 CM", description:"Um pequeno explorador para dar vida à sua mesa. Consulte as opções de cores e o tamanho do vaso.", image:"assets/astro-planter.png", alt:"Astro planter roxo"},
  dock: {name:"Dock de controle", price:49.90, category:"SETUP / 15 CM", description:"Deixe seu controle em destaque e mantenha o setup organizado com este suporte impresso em 3D.", image:"assets/dock-controle.png", alt:"Dock verde para controle"},
  vaso: {name:"Vaso Órbita", price:89.90, category:"DECOR / 18 CM", description:"Peça decorativa com linhas espirais e visual orgânico. Uma forma diferente de destacar suas plantas.", image:"assets/vaso-orbita.png", alt:"Vaso Órbita branco"},
  torre: {name:"Torre do mago", price:119.90, category:"RPG / 20 CM", description:"Um cenário de fantasia para sua coleção, mesa de RPG ou setup. Consulte as opções de acabamento.", image:"assets/torre-mago.png", alt:"Torre azul do mago"},
  cable: {name:"Cable buddy", price:29.90, category:"SETUP / KIT COM 3", description:"Trio de personagens para organizar cabos e deixar sua mesa com mais personalidade.", image:"assets/cable-buddy.png", alt:"Cable buddy em formato de personagens"},
  bot: {name:"Mini bot", price:39.90, category:"COLECIONÁVEL / 9 CM", description:"Um pequeno robô colecionável impresso em 3D. Escolha sua cor favorita ao falar com o lab.", image:"assets/mini-bot.png", alt:"Mini bot dourado"}
};
const cart = {};
const money = value => value.toLocaleString("pt-BR",{style:"currency",currency:"BRL"});
const cartCount = document.querySelector("#cartCount");
const cartDrawer = document.querySelector("#cartDrawer");
const backdrop = document.querySelector("#drawerBackdrop");
const toast = document.querySelector("#toast");

function notify(message){
  toast.textContent = message;
  toast.classList.add("show");
  clearTimeout(notify.timer);
  notify.timer = setTimeout(()=>toast.classList.remove("show"),1900);
}
function addToCart(id){
  if(!products[id]) return;
  cart[id] = (cart[id] || 0) + 1;
  updateCart();
  notify(`${products[id].name} adicionado à sacola`);
}
function updateCart(){
  const count = Object.values(cart).reduce((sum,n)=>sum+n,0);
  cartCount.textContent = count;
  const items = document.querySelector("#cartItems");
  const entries = Object.entries(cart).filter(([,qty])=>qty>0);
  if(!entries.length){
    items.innerHTML = '<p class="cart-empty">Sua sacola ainda está vazia.</p>';
  } else {
    items.innerHTML = entries.map(([id,qty])=>`<div class="cart-line"><strong>${products[id].name}</strong><small>${qty} × ${money(products[id].price)}</small><button data-remove="${id}" aria-label="Remover ${products[id].name}">−</button></div>`).join("");
  }
  const total = entries.reduce((sum,[id,qty])=>sum+products[id].price*qty,0);
  document.querySelector("#cartTotal").textContent = money(total);
  const message = entries.map(([id,qty])=>`${qty}x ${products[id].name} — ${money(products[id].price*qty)}`).join("\n");
  const checkout = document.querySelector("#checkoutLink");
  checkout.href = `https://wa.me/5511959990386?text=${encodeURIComponent("Olá! Quero finalizar meu pedido:\n"+message+"\nTotal: "+money(total))}`;
}
function openCart(){
  cartDrawer.classList.add("open");backdrop.classList.add("open");
  cartDrawer.setAttribute("aria-hidden","false");
}
function closeCart(){
  cartDrawer.classList.remove("open");backdrop.classList.remove("open");
  cartDrawer.setAttribute("aria-hidden","true");
}
document.addEventListener("click",e=>{
  const add = e.target.closest("[data-add]");
  if(add){e.preventDefault();addToCart(add.dataset.add);}
  const remove = e.target.closest("[data-remove]");
  if(remove){const id=remove.dataset.remove;cart[id]=Math.max(0,(cart[id]||0)-1);updateCart();}
});
document.querySelector("#cartButton").addEventListener("click",openCart);
document.querySelector("#closeCart").addEventListener("click",closeCart);
backdrop.addEventListener("click",closeCart);
document.addEventListener("keydown",e=>{if(e.key==="Escape")closeCart();});

let activeFilter = "Tudo";
function filterProducts(){
  const query = document.querySelector("#searchInput").value.trim().toLocaleLowerCase("pt-BR");
  const cards = [...document.querySelectorAll(".product-card")];
  const sort = document.querySelector("#sortSelect").value;
  cards.sort((a,b)=>{
    if(sort==="low") return Number(a.dataset.price)-Number(b.dataset.price);
    if(sort==="high") return Number(b.dataset.price)-Number(a.dataset.price);
    return 0;
  });
  const grid = document.querySelector("#productGrid");
  cards.forEach(card=>{
    const matchesCategory = activeFilter==="Tudo" || card.dataset.category===activeFilter;
    const matchesText = card.dataset.name.toLocaleLowerCase("pt-BR").includes(query);
    card.hidden = !(matchesCategory && matchesText);
    grid.appendChild(card);
  });
  const visible = cards.filter(card=>!card.hidden).length;
  document.querySelector("#pieceCount").textContent = `${String(visible).padStart(2,"0")} PEÇAS`;
  document.querySelector("#emptyState").hidden = visible!==0;
  document.querySelector(".all-seen").hidden = visible===0;
}
document.querySelectorAll(".filter").forEach(button=>button.addEventListener("click",()=>{
  document.querySelector(".filter.active").classList.remove("active");
  button.classList.add("active");activeFilter=button.dataset.filter;filterProducts();
}));
document.querySelector("#searchInput").addEventListener("input",filterProducts);
document.querySelector("#sortSelect").addEventListener("change",filterProducts);

// The featured product detail is available through #produto-dragao.
function route(){
  const isProduct = location.hash.startsWith("#produto-");
  document.querySelector("#catalogPage").hidden = isProduct;
  document.querySelector("#productPage").hidden = !isProduct;
  if(isProduct){
    const id = location.hash.replace("#produto-","");
    const product = products[id] || products.dragao;
    document.querySelector("#detailTitle").textContent = product.name;
    document.querySelector("#detailDescription").textContent = product.description;
    document.querySelector("#detailPrice").textContent = money(product.price);
    const image = document.querySelector("#detailImage");
    image.src = product.image;
    image.alt = product.alt;
    document.querySelector(".detail-copy .product-meta").textContent = product.category;
    document.querySelector(".detail-image .tag").textContent = id==="dragao" ? "LOOT DA SEMANA" : "PEÇA DO DROP";
    document.querySelector(".detail-drop").textContent = id==="dragao" ? "DROP / 007" : "LAYER / LAB";
    document.querySelector("#whatsappLink").href = `https://wa.me/5511959990386?text=${encodeURIComponent("Olá! Tenho interesse na peça "+product.name+".")}`;
  }
  window.scrollTo(0,0);
}
window.addEventListener("hashchange",route);
route();
updateCart();

// Replace these contact destinations with the store's real WhatsApp number and Instagram profile.

// Menu compacto para navegar pelas categorias.
const menuButton = document.querySelector("#menuButton");
menuButton.addEventListener("click",()=>{
  let menu = document.querySelector("#quickMenu");
  if(menu){menu.remove();menuButton.setAttribute("aria-expanded","false");return;}
  menu = document.createElement("nav");
  menu.id = "quickMenu";
  menu.className = "quick-menu";
  menu.innerHTML = '<a href="#catalogo" data-menu-filter="Tudo">Todas as peças</a><a href="#catalogo" data-menu-filter="Geek">Geek</a><a href="#catalogo" data-menu-filter="Decor">Decor</a><a href="#catalogo" data-menu-filter="Setup">Setup</a>';
  document.querySelector(".site-header").appendChild(menu);
  menuButton.setAttribute("aria-expanded","true");
  menu.addEventListener("click",e=>{
    const link=e.target.closest("[data-menu-filter]");
    if(!link)return;
    e.preventDefault();
    location.hash="";
    document.querySelector("#catalogPage").hidden=false;
    document.querySelector("#productPage").hidden=true;
    const filter=link.dataset.menuFilter;
    document.querySelectorAll(".filter").forEach(b=>b.classList.toggle("active",b.dataset.filter===filter));
    activeFilter=filter;filterProducts();menu.remove();menuButton.setAttribute("aria-expanded","false");
    document.querySelector("#catalogPage").scrollIntoView({behavior:"smooth"});
  });
});
