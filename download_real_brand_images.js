const fs = require('fs');
const path = require('path');
const https = require('https');
const http = require('http');

const destDir = path.join(__dirname, 'frontend', 'public', 'products');
if (!fs.existsSync(destDir)) {
  fs.mkdirSync(destDir, { recursive: true });
}

const items = [
  {
    file: 'minimalist_niacinamide.png',
    url: 'https://beminimalist.co/cdn/shop/files/Nia10New.png?v=1721398127&width=480'
  },
  {
    file: 'minimalist_salicylic.png',
    url: 'https://beminimalist.co/cdn/shop/products/SalicylicAcid2_1200-1-min.png?v=1646458899&width=480'
  },
  {
    file: 'minimalist_vit_c.png',
    url: 'https://beminimalist.co/cdn/shop/products/VitaminC10_1200-1-min.png?v=1646543848&width=480'
  },
  {
    file: 'minimalist_spf50.jpg',
    url: 'https://beminimalist.co/cdn/shop/files/SPF50New.jpg?v=1756795782&width=480'
  },
  {
    file: 'ordinary_niacinamide.jpg',
    url: 'https://cdn.media.amplience.net/i/deciem/Niacinamide-Image?fmt=auto&w=600'
  },
  {
    file: 'ordinary_hyaluronic.jpg',
    url: 'https://cdn.media.amplience.net/i/deciem/Hyaluronic-Image?fmt=auto&w=600'
  },
  {
    file: 'ordinary_peeling.jpg',
    url: 'https://cdn.media.amplience.net/i/deciem/Peeling-Image?fmt=auto&w=600'
  },
  {
    file: 'ordinary_glycolic.jpg',
    url: 'https://cdn.media.amplience.net/i/deciem/Glycolic-Image?fmt=auto&w=600'
  },
  {
    file: 'deconstruct_brightening.webp',
    url: 'https://thedeconstruct.in/cdn/shop/files/final-resized-imagesArtboard-5.webp?width=600'
  },
  {
    file: 'deconstruct_breakout.webp',
    url: 'https://thedeconstruct.in/cdn/shop/files/clearing.webp?width=600'
  },
  {
    file: 'deconstruct_vit_c.webp',
    url: 'https://thedeconstruct.in/cdn/shop/files/VITAMIN_C.webp?width=600'
  },
  {
    file: 'derma_co_sunscreen.jpg',
    url: 'https://thedermaco.com/cdn/shop/files/5_1a9575c9-e7a2-4b1c-9afb-c1741add8e58.jpg?v=1791452606&width=600'
  },
  {
    file: 'derma_co_facewash.jpg',
    url: 'https://thedermaco.com/cdn/shop/files/PDP_50gm.jpg?v=1783575300&width=600'
  },
  {
    file: 'cerave_cleanser.jpg',
    url: 'https://www.cerave.com/-/media/project/loreal/brand-sites/cerave/americas/us/skincare/cleansers/hydrating-facial-cleanser/photos/2026/700x785/hfc-atf-1-700x785-v1.jpg'
  },
  {
    file: 'cerave_cream.jpg',
    url: 'https://www.cerave.com/-/media/project/loreal/brand-sites/cerave/americas/us/skincare/moisturizers/facial-moisturizers/skin-renewing-brightening-lotion-with-spf-30/desktop/v2/cerave-skin-renewing-packshot-front-700x785-v1.jpg'
  },
  {
    file: 'cosrx_snail.png',
    url: 'https://www.cosrx.com/cdn/shop/files/snail-co.png?v=1789693978&width=600'
  }
];

function download(url, filePath) {
  return new Promise((resolve, reject) => {
    const client = url.startsWith('https') ? https : http;
    client.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, (res) => {
      if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
        return download(res.headers.location, filePath).then(resolve).catch(reject);
      }
      if (res.statusCode !== 200) {
        return reject(new Error(`Failed to download ${url}: status code ${res.statusCode}`));
      }
      const stream = fs.createWriteStream(filePath);
      res.pipe(stream);
      stream.on('finish', () => {
        stream.close();
        resolve();
      });
      stream.on('error', reject);
    }).on('error', reject);
  });
}

async function run() {
  for (const item of items) {
    const target = path.join(destDir, item.file);
    try {
      console.log(`Downloading ${item.file}...`);
      await download(item.url, target);
      console.log(`✓ Saved ${item.file}`);
    } catch (err) {
      console.error(`✗ Error downloading ${item.file}:`, err.message);
    }
  }
  console.log('Finished downloading original brand pictures!');
}

run();
