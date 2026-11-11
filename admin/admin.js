const colorList = document.querySelector("#colorList");
const colorsJson = document.querySelector("#colorsJson");

if(colorList && colorsJson){
  let colors = JSON.parse(colorList.dataset.initialColors || "[]");

  function syncColors(){
    colorsJson.value = JSON.stringify(colors);
    colorList.replaceChildren();
    colors.forEach((color,index)=>{
      const chip = document.createElement("div");
      chip.className = "color-chip";
      const picker = document.createElement("input");
      picker.type = "color";
      picker.value = color;
      picker.setAttribute("aria-label",`Alterar cor ${index+1}`);
      picker.addEventListener("change",()=>{
        colors[index] = picker.value.toLowerCase();
        code.textContent = colors[index].toUpperCase();
        colorsJson.value = JSON.stringify(colors);
      });
      const code = document.createElement("span");
      code.textContent = color.toUpperCase();
      const remove = document.createElement("button");
      remove.type = "button";
      remove.className = "color-remove";
      remove.setAttribute("aria-label",`Remover cor ${color}`);
      remove.textContent = "×";
      remove.addEventListener("click",()=>{
        colors.splice(index,1);
        syncColors();
      });
      chip.append(picker,code,remove);
      colorList.appendChild(chip);
    });
  }

  document.querySelector("#addColor").addEventListener("click",()=>{
    const color = document.querySelector("#newColor").value.toLowerCase();
    if(!colors.includes(color)){
      colors.push(color);
      syncColors();
    }
  });

  async function dominantColors(source){
    const image = new Image();
    if(source instanceof Blob){
      image.src = URL.createObjectURL(source);
    }else{
      image.src = source;
    }
    await new Promise((resolve,reject)=>{
      image.onload = resolve;
      image.onerror = ()=>reject(new Error("Não foi possível ler a imagem selecionada."));
    });

    const canvas = document.createElement("canvas");
    const size = 56;
    canvas.width = size;
    canvas.height = size;
    const context = canvas.getContext("2d",{willReadFrequently:true});
    context.drawImage(image,0,0,size,size);
    const pixels = context.getImageData(0,0,size,size).data;
    const buckets = new Map();

    for(let i=0;i<pixels.length;i+=4){
      if(pixels[i+3]<200) continue;
      const red = Math.min(255,Math.round(pixels[i]/16)*16);
      const green = Math.min(255,Math.round(pixels[i+1]/16)*16);
      const blue = Math.min(255,Math.round(pixels[i+2]/16)*16);
      const key = `${red},${green},${blue}`;
      buckets.set(key,(buckets.get(key)||0)+1);
    }

    const candidates = [...buckets.entries()]
      .sort((a,b)=>b[1]-a[1])
      .map(([key])=>key.split(",").map(Number));
    const selected = [];
    const minimumDistance = 45;
    for(const candidate of candidates){
      const distinct = selected.every(color=>{
        const distance = Math.sqrt(
          (color[0]-candidate[0])**2+
          (color[1]-candidate[1])**2+
          (color[2]-candidate[2])**2
        );
        return distance >= minimumDistance;
      });
      if(distinct){
        selected.push(candidate);
        if(selected.length===3) break;
      }
    }
    if(source instanceof Blob) URL.revokeObjectURL(image.src);
    return selected.map(([red,green,blue])=>"#"+[red,green,blue].map(value=>value.toString(16).padStart(2,"0")).join(""));
  }

  document.querySelector("#suggestColors").addEventListener("click",async event=>{
    const button = event.currentTarget;
    const message = document.querySelector("#colorSuggestionMessage");
    const file = document.querySelector('input[name="imagem"]').files[0];
    const currentImage = document.querySelector(".image-input .current-image img");
    const source = file || currentImage?.src;
    if(!source){
      message.textContent = "Selecione uma imagem principal primeiro.";
      return;
    }
    button.disabled = true;
    try{
      const suggestions = await dominantColors(source);
      if(suggestions.length===0) throw new Error("Não foi possível identificar cores nesta imagem.");
      colors = suggestions;
      syncColors();
      message.textContent = `${suggestions.length} cores sugeridas. Você pode alterar, adicionar ou remover cada uma.`;
    }catch(error){
      message.textContent = error.message;
    }finally{
      button.disabled = false;
    }
  });

  syncColors();
}

document.querySelectorAll('input[type="file"]').forEach(input=>{
  input.addEventListener("change",()=>{
    const preview = input.parentElement.querySelector(".image-preview");
    if(!preview) return;
    if(input.dataset.previewUrl) URL.revokeObjectURL(input.dataset.previewUrl);
    const file = input.files[0];
    if(!file){
      preview.hidden = true;
      preview.removeAttribute("src");
      return;
    }
    input.dataset.previewUrl = URL.createObjectURL(file);
    preview.src = input.dataset.previewUrl;
    preview.hidden = false;
  });
});

document.querySelectorAll("form[data-confirm]").forEach(form=>{
  form.addEventListener("submit",event=>{
    if(!window.confirm(form.dataset.confirm)) event.preventDefault();
  });
});
