const products = window.KAIROS_PRODUCTS || {};
const cart = {};
const money = value => Number(value).toLocaleString("pt-BR",{style:"currency",currency:"BRL"});
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
  const product = products[id];
  if(!product) return;
  cart[id] = (cart[id] || 0) + 1;
  updateCart();
  notify(`${product.name} adicionado à sacola`);
}
function updateCart(){
  const count = Object.values(cart).reduce((sum,n)=>sum+n,0);
  cartCount.textContent = count;
  const items = document.querySelector("#cartItems");
  const entries = Object.entries(cart).filter(([id,qty])=>qty>0 && products[id]);
  items.replaceChildren();

  if(!entries.length){
    const empty = document.createElement("p");
    empty.className = "cart-empty";
    empty.textContent = "Sua sacola ainda está vazia.";
    items.appendChild(empty);
  } else {
    entries.forEach(([id,qty])=>{
      const line = document.createElement("div");
      line.className = "cart-line";
      const name = document.createElement("strong");
      name.textContent = products[id].name;
      const price = document.createElement("small");
      price.textContent = `${qty} × ${money(products[id].price)}`;
      const remove = document.createElement("button");
      remove.dataset.remove = id;
      remove.setAttribute("aria-label",`Remover ${products[id].name}`);
      remove.textContent = "−";
      line.append(name,price,remove);
      items.appendChild(line);
    });
  }

  const total = entries.reduce((sum,[id,qty])=>sum+products[id].price*qty,0);
  document.querySelector("#cartTotal").textContent = money(total);
  const message = entries.map(([id,qty])=>`${qty}x ${products[id].name} — ${money(products[id].price*qty)}`).join("\n");
  document.querySelector("#checkoutLink").href = `https://wa.me/5511959990386?text=${encodeURIComponent("Olá! Quero finalizar meu pedido:\n"+message+"\nTotal: "+money(total))}`;
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
  if(remove){
    const id = remove.dataset.remove;
    cart[id] = Math.max(0,(cart[id] || 0)-1);
    updateCart();
  }
});
document.querySelector("#cartButton").addEventListener("click",openCart);
document.querySelector("#closeCart").addEventListener("click",closeCart);
backdrop.addEventListener("click",closeCart);
document.addEventListener("keydown",e=>{if(e.key==="Escape")closeCart();});

let activeFilter = "Tudo";
function filterProducts(){
  const query = document.querySelector("#searchInput").value.trim().toLocaleLowerCase("pt-BR");
  const cards = [...document.querySelectorAll(".product-card")];
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
  document.querySelector(".filter.active")?.classList.remove("active");
  button.classList.add("active");
  activeFilter = button.dataset.filter;
  filterProducts();
}));
document.querySelector("#searchInput").addEventListener("input",filterProducts);

function route(){
  const isProduct = location.hash.startsWith("#produto-");
  const catalogPage = document.querySelector("#catalogPage");
  const productPage = document.querySelector("#productPage");
  catalogPage.hidden = isProduct;
  productPage.hidden = !isProduct;

  if(isProduct){
    const id = location.hash.replace("#produto-","");
    const product = products[id];
    if(!product){
      location.hash = "";
      return;
    }

    document.querySelector("#detailTitle").textContent = product.name;
    document.querySelector("#detailDescription").textContent = product.description;
    document.querySelector("#detailPrice").textContent = money(product.price);
    const image = document.querySelector("#detailImage");
    image.src = product.image;
    image.alt = product.alt;
    document.querySelector(".detail-copy .product-meta").textContent =
      `${product.category}${product.dimensions ? " / " + product.dimensions : ""}`;
    document.querySelector(".detail-image .tag").textContent = product.badge || "PEÇA DO DROP";
    document.querySelector(".detail-drop").textContent = product.dropCode;
    document.querySelector("#whatsappLink").href =
      `https://wa.me/5511959990386?text=${encodeURIComponent("Olá! Tenho interesse na peça "+product.name+".")}`;
  }
  window.scrollTo(0,0);
}
window.addEventListener("hashchange",route);
route();
updateCart();

function enableInfiniteFeaturedScroll(){
  const track = document.querySelector("#featuredTrack");
  if(!track) return;
  const originals = [...track.children];
  if(originals.length < 2) return;

  const firstClone = originals[0].cloneNode(true);
  const lastClone = originals[originals.length-1].cloneNode(true);
  firstClone.setAttribute("aria-hidden","true");
  lastClone.setAttribute("aria-hidden","true");
  track.appendChild(firstClone);
  track.insertBefore(lastClone,track.firstChild);

  const itemWidth = ()=>track.clientWidth;
  const resetPosition = ()=>{ track.scrollLeft = itemWidth(); };
  resetPosition();
  window.addEventListener("resize",resetPosition);

  let scrollTimer;
  track.addEventListener("scroll",()=>{
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(()=>{
      const width = itemWidth();
      if(track.scrollLeft < width * .5){
        track.classList.add("is-resetting");
        track.scrollLeft = width * originals.length;
        requestAnimationFrame(()=>track.classList.remove("is-resetting"));
      } else if(track.scrollLeft >= width * (originals.length + .5)){
        track.classList.add("is-resetting");
        track.scrollLeft = width;
        requestAnimationFrame(()=>track.classList.remove("is-resetting"));
      }
    },120);
  },{passive:true});
}
enableInfiniteFeaturedScroll();

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
    document.querySelectorAll(".filter").forEach(button=>button.classList.toggle("active",button.dataset.filter===filter));
    activeFilter=filter;
    filterProducts();
    menu.remove();
    menuButton.setAttribute("aria-expanded","false");
    document.querySelector("#catalogPage").scrollIntoView({behavior:"smooth"});
  });
});
