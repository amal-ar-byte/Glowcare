const fs = require('fs');
const path = require('path');

const outDir = path.join(__dirname, 'frontend', 'public', 'products');
if (!fs.existsSync(outDir)) {
  fs.mkdirSync(outDir, { recursive: true });
}

function generateBottleSVG({
  brand,
  name,
  subtext,
  bottleType = 'dropper', // 'dropper', 'pump', 'tube', 'jar'
  bottleColor = '#F5F2EB',
  liquidColor = '#E8E4DA',
  labelColor = '#FFFFFF',
  textColor = '#111111',
  accentColor = '#E88B9E'
}) {
  let bottleShape = '';
  
  if (bottleType === 'dropper') {
    bottleShape = `
      <!-- Dropper Top -->
      <path d="M135 45 C135 32 165 32 165 45 L165 65 L135 65 Z" fill="#222222" />
      <!-- Dropper Rim/Cap -->
      <rect x="130" y="65" width="40" height="25" rx="3" fill="#EEEEEE" stroke="#CCCCCC" stroke-width="1.5" />
      <rect x="133" y="68" width="34" height="4" fill="#DDDDDD" />
      <!-- Glass Shoulder -->
      <path d="M130 90 L170 90 L195 125 L105 125 Z" fill="${bottleColor}" opacity="0.95" />
      <!-- Glass Body -->
      <rect x="105" y="125" width="90" height="175" rx="10" fill="${bottleColor}" />
      <!-- Liquid Level -->
      <rect x="110" y="145" width="80" height="150" rx="6" fill="${liquidColor}" opacity="0.75" />
      <!-- Label -->
      <rect x="112" y="150" width="76" height="125" rx="4" fill="${labelColor}" stroke="#E5E5E5" stroke-width="1" />
      <!-- Glass Specular Highlight -->
      <path d="M110 128 L115 128 L115 295 L110 295 Z" fill="#FFFFFF" opacity="0.6" />
    `;
  } else if (bottleType === 'pump') {
    bottleShape = `
      <!-- Pump Head -->
      <path d="M140 35 L175 35 C185 35 185 45 175 45 L155 45 L155 70 L140 70 Z" fill="#2B2B2B" />
      <rect x="135" y="70" width="30" height="20" rx="3" fill="#FFFFFF" stroke="#CCCCCC" stroke-width="1" />
      <!-- Tall Body -->
      <rect x="100" y="90" width="100" height="215" rx="14" fill="${bottleColor}" stroke="#E8E2D8" stroke-width="1.5" />
      <!-- Liquid -->
      <rect x="106" y="105" width="88" height="195" rx="10" fill="${liquidColor}" opacity="0.6" />
      <!-- Label -->
      <rect x="108" y="120" width="84" height="150" rx="6" fill="${labelColor}" stroke="#EAE5DE" stroke-width="1" />
      <!-- Specular Reflection -->
      <path d="M104 95 L110 95 L110 300 L104 300 Z" fill="#FFFFFF" opacity="0.5" />
    `;
  } else if (bottleType === 'tube') {
    bottleShape = `
      <!-- Squeeze Tube Body -->
      <path d="M110 260 L95 70 C95 65 205 65 205 70 L190 260 Z" fill="${bottleColor}" stroke="#E5DFD5" stroke-width="1.5" />
      <!-- Tube Crimp Top -->
      <rect x="94" y="60" width="112" height="12" rx="2" fill="#E8E2D6" stroke="#D5CEC2" stroke-width="1" />
      <!-- Cap at Bottom -->
      <rect x="120" y="260" width="60" height="35" rx="6" fill="#FFFFFF" stroke="#CCCCCC" stroke-width="1.5" />
      <!-- Label Area -->
      <rect x="108" y="95" width="84" height="135" rx="6" fill="${labelColor}" opacity="0.95" />
      <!-- Highlights -->
      <path d="M102 75 L108 75 L116 250 L110 250 Z" fill="#FFFFFF" opacity="0.4" />
    `;
  } else { // 'jar'
    bottleShape = `
      <!-- Jar Lid -->
      <rect x="90" y="120" width="120" height="35" rx="8" fill="#FFFFFF" stroke="#CCCCCC" stroke-width="1.5" />
      <!-- Jar Glass Base -->
      <rect x="95" y="155" width="110" height="110" rx="14" fill="${bottleColor}" stroke="#E2DBD0" stroke-width="1.5" />
      <rect x="100" y="165" width="100" height="95" rx="10" fill="${liquidColor}" opacity="0.7" />
      <!-- Jar Label -->
      <rect x="105" y="175" width="90" height="70" rx="4" fill="${labelColor}" stroke="#EAE3D9" stroke-width="1" />
    `;
  }

  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 340" width="100%" height="100%">
  <defs>
    <radialGradient id="bgGrad" cx="50%" cy="40%" r="55%">
      <stop offset="0%" stop-color="#FFFDF9" />
      <stop offset="70%" stop-color="#FBF3EC" />
      <stop offset="100%" stop-color="#F4E9DF" />
    </radialGradient>
    <filter id="shadow" x="-10%" y="-10%" width="120%" height="130%">
      <feDropShadow dx="0" dy="12" stdDeviation="12" flood-color="#8C7A6B" flood-opacity="0.18" />
    </filter>
  </defs>

  <!-- Background Canvas -->
  <rect width="300" height="340" fill="url(#bgGrad)" rx="16" />

  <!-- Shadow under bottle -->
  <ellipse cx="150" cy="308" rx="55" ry="12" fill="#2C241E" opacity="0.16" filter="blur(6px)" />

  <!-- Bottle Graphic -->
  <g filter="url(#shadow)">
    ${bottleShape}

    <!-- Brand Header -->
    <text x="150" y="${bottleType === 'jar' ? 192 : 168}" font-family="system-ui, -apple-system, sans-serif" font-size="7.5" font-weight="900" text-anchor="middle" fill="${accentColor}" letter-spacing="1.2">${brand.toUpperCase()}</text>
    
    <!-- Accent Line -->
    <line x1="126" y1="${bottleType === 'jar' ? 198 : 175}" x2="174" y2="${bottleType === 'jar' ? 198 : 175}" stroke="${accentColor}" stroke-width="1" />

    <!-- Product Title -->
    <text x="150" y="${bottleType === 'jar' ? 212 : 192}" font-family="system-ui, -apple-system, sans-serif" font-size="8.5" font-weight="800" text-anchor="middle" fill="${textColor}">
      ${name.length > 18 ? name.substring(0, 18) : name}
    </text>
    ${name.length > 18 ? `<text x="150" y="${bottleType === 'jar' ? 222 : 204}" font-family="system-ui, -apple-system, sans-serif" font-size="7.5" font-weight="700" text-anchor="middle" fill="${textColor}">${name.substring(18)}</text>` : ''}

    <!-- Subtext / Active % -->
    <text x="150" y="${bottleType === 'jar' ? 234 : 224}" font-family="system-ui, -apple-system, sans-serif" font-size="6.8" font-weight="600" text-anchor="middle" fill="#666666">${subtext}</text>

    <!-- Clinical Badge / Barcode icon -->
    <rect x="138" y="${bottleType === 'jar' ? 238 : 242}" width="24" height="4" rx="1" fill="#CCCCCC" />
    <text x="150" y="${bottleType === 'jar' ? 245 : 256}" font-family="system-ui, -apple-system, sans-serif" font-size="5.5" font-weight="700" text-anchor="middle" fill="#999999">30ml / 1.0 fl oz</text>
  </g>
</svg>`;
}

const products = [
  // 1. Minimalist Niacinamide
  {
    filename: 'minimalist_niacinamide.svg',
    brand: 'Minimalist',
    name: 'Niacinamide 10%',
    subtext: '+ EU Zinc PCA 1%',
    bottleType: 'dropper',
    bottleColor: '#362417', // Dark Amber Glass
    liquidColor: '#4A3423',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#1A1A1A'
  },
  // 2. Minimalist Salicylic Acid
  {
    filename: 'minimalist_salicylic.svg',
    brand: 'Minimalist',
    name: 'Salicylic Acid 2%',
    subtext: '+ Oligopeptide-10',
    bottleType: 'dropper',
    bottleColor: '#362417', // Dark Amber Glass
    liquidColor: '#4A3423',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#1A1A1A'
  },
  // 3. Minimalist Vitamin C
  {
    filename: 'minimalist_vit_c.svg',
    brand: 'Minimalist',
    name: 'Vitamin C 10%',
    subtext: '+ Acetyl Glucosamine',
    bottleType: 'dropper',
    bottleColor: '#362417',
    liquidColor: '#4A3423',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#D97706'
  },
  // 4. Minimalist Multi-Vitamin Sunscreen
  {
    filename: 'minimalist_spf50.svg',
    brand: 'Minimalist',
    name: 'SPF 50 Multi-Vitamin',
    subtext: 'PA++++ Broad Spectrum',
    bottleType: 'tube',
    bottleColor: '#FFFFFF',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#DC2626'
  },
  // 5. The Ordinary Niacinamide 10% + Zinc 1%
  {
    filename: 'ordinary_niacinamide.svg',
    brand: 'The Ordinary.',
    name: 'Niacinamide 10%',
    subtext: '+ Zinc 1% High-Strength',
    bottleType: 'dropper',
    bottleColor: '#FAFAFA', // Frosted White Glass
    liquidColor: '#FFFFFF',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#333333'
  },
  // 6. The Ordinary Hyaluronic Acid 2% + B5
  {
    filename: 'ordinary_hyaluronic.svg',
    brand: 'The Ordinary.',
    name: 'Hyaluronic Acid 2%',
    subtext: '+ Vitamin B5 Hydration',
    bottleType: 'dropper',
    bottleColor: '#F5F9FD',
    liquidColor: '#E6F0FA',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#0284C7'
  },
  // 7. The Ordinary AHA 30% + BHA 2% Peeling Solution
  {
    filename: 'ordinary_peeling.svg',
    brand: 'The Ordinary.',
    name: 'AHA 30% + BHA 2%',
    subtext: '10-Minute Peeling',
    bottleType: 'dropper',
    bottleColor: '#FAF8F8',
    liquidColor: '#7F1D1D', // Deep Tasmanian Pepper Blood Red
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#991B1B'
  },
  // 8. The Ordinary Glycolic Acid 7%
  {
    filename: 'ordinary_glycolic.svg',
    brand: 'The Ordinary.',
    name: 'Glycolic Acid 7%',
    subtext: 'Toning Solution 240ml',
    bottleType: 'pump',
    bottleColor: '#FFFDF0',
    liquidColor: '#FDF6E2', // Peach Tint
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#B45309'
  },
  // 9. Deconstruct Brightening Serum
  {
    filename: 'deconstruct_brightening.svg',
    brand: 'Deconstruct',
    name: 'Brightening Serum',
    subtext: '10% Niacinamide + 0.3% AA',
    bottleType: 'dropper',
    bottleColor: '#FFFFFF',
    liquidColor: '#FFF8F4',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#EA580C' // Iconic Orange
  },
  // 10. Deconstruct Breakout Control
  {
    filename: 'deconstruct_breakout.svg',
    brand: 'Deconstruct',
    name: 'Breakout Control',
    subtext: '3.3% Potassium Azeloyl',
    bottleType: 'dropper',
    bottleColor: '#FFFFFF',
    liquidColor: '#F0FDF4',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#16A34A' // Iconic Green
  },
  // 11. Deconstruct Salicylic Cleanser
  {
    filename: 'deconstruct_cleanser.svg',
    brand: 'Deconstruct',
    name: 'Pore Control Wash',
    subtext: '0.5% Salicylic + 1% Niacinamide',
    bottleType: 'pump',
    bottleColor: '#F0F9FF',
    liquidColor: '#E0F2FE',
    labelColor: '#FFFFFF',
    textColor: '#111111',
    accentColor: '#0284C7'
  },
  // 12. The Derma Co 1% Hyaluronic Sunscreen
  {
    filename: 'derma_co_sunscreen.svg',
    brand: 'The Derma Co',
    name: '1% Hyaluronic Gel',
    subtext: 'Aqua Gel SPF 50 PA++++',
    bottleType: 'tube',
    bottleColor: '#FFFFFF',
    labelColor: '#F0F9FF',
    textColor: '#0F172A',
    accentColor: '#2563EB'
  },
  // 13. The Derma Co 2% Niacinamide Wash
  {
    filename: 'derma_co_niacinamide.svg',
    brand: 'The Derma Co',
    name: '2% Niacinamide Wash',
    subtext: '+ Cica Extract Clear Skin',
    bottleType: 'pump',
    bottleColor: '#F8FAFC',
    liquidColor: '#F1F5F9',
    labelColor: '#FFFFFF',
    textColor: '#0F172A',
    accentColor: '#0284C7'
  },
  // 14. CeraVe Hydrating Cleanser
  {
    filename: 'cerave_cleanser.svg',
    brand: 'CeraVe',
    name: 'Hydrating Cleanser',
    subtext: '3 Essential Ceramides + HA',
    bottleType: 'pump',
    bottleColor: '#FFFFFF',
    liquidColor: '#ECFDF5',
    labelColor: '#FFFFFF',
    textColor: '#064E3B',
    accentColor: '#059669' // CeraVe Green
  },
  // 15. CeraVe Moisturizing Cream
  {
    filename: 'cerave_cream.svg',
    brand: 'CeraVe',
    name: 'Moisturizing Cream',
    subtext: 'For Dry to Very Dry Skin',
    bottleType: 'jar',
    bottleColor: '#FFFFFF',
    liquidColor: '#EFF6FF',
    labelColor: '#FFFFFF',
    textColor: '#1E3A8A',
    accentColor: '#2563EB' // CeraVe Blue
  },
  // 16. COSRX Snail Mucin
  {
    filename: 'cosrx_snail.svg',
    brand: 'COSRX',
    name: 'Advanced Snail 96',
    subtext: 'Mucin Power Essence 100ml',
    bottleType: 'pump',
    bottleColor: '#FAFAFA',
    liquidColor: '#F4F4F5',
    labelColor: '#FFFFFF',
    textColor: '#18181B',
    accentColor: '#E11D48'
  },
  // 17. COSRX Low pH Cleanser
  {
    filename: 'cosrx_cleanser.svg',
    brand: 'COSRX',
    name: 'Good Morning Gel',
    subtext: 'Low pH Gentle Cleanser',
    bottleType: 'tube',
    bottleColor: '#FFFFFF',
    labelColor: '#FEF9C3',
    textColor: '#713F12',
    accentColor: '#EAB308' // Yellow
  },
  // 18. Paula's Choice 2% BHA Liquid
  {
    filename: 'paulas_choice_bha.svg',
    brand: "Paula's Choice",
    name: '2% BHA Liquid',
    subtext: 'Skin Perfecting Exfoliant',
    bottleType: 'pump',
    bottleColor: '#374151', // Iconic Dark Grey
    liquidColor: '#4B5563',
    labelColor: '#1F2937',
    textColor: '#FFFFFF',
    accentColor: '#F3F4F6'
  }
];

for (const p of products) {
  const svg = generateBottleSVG(p);
  fs.writeFileSync(path.join(outDir, p.filename), svg, 'utf8');
  console.log(`Generated: ${p.filename}`);
}

console.log('All 18 real brand skincare SVGs successfully created!');
