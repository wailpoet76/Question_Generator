<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Flutter Roadmap Sectors</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .sector-button:hover {
      filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.7));
      transform: scale(1.05);
    }

    .sector-button {
      transition: all 0.3s ease;
      cursor: pointer;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="relative w-[600px] h-[600px]">

    <!-- Center Logo -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
      <img src="https://upload.wikimedia.org/wikipedia/commons/1/17/Google-flutter-logo.png" class="w-16 mx-auto mb-2" />
      <h2 class="text-xl font-bold">Flutter Developer</h2>
      <p class="text-gray-500">Roadmap</p>
    </div>

    <!-- SVG Sectors -->
    <svg class="w-full h-full" viewBox="0 0 600 600">
      <!-- Generate 12 sectors -->
      <g transform="translate(300,300)">
        <!-- JavaScript will insert sectors here -->
      </g>
    </svg>
  </div>

  <script>
    const topics = [
      { name: 'Dart', color: '#FDB813', icon: '🟡' },
      { name: 'Firebase', color: '#F5820D', icon: '🔥' },
      { name: 'Maps', color: '#F44336', icon: '🗺️' },
      { name: 'Widgets', color: '#9C27B0', icon: '🧱' },
      { name: 'Provider', color: '#673AB7', icon: '🧩' },
      { name: 'Bloc', color: '#3F51B5', icon: '📦' },
      { name: 'Data Structure', color: '#2196F3', icon: '🗂️' },
      { name: 'Algorithms', color: '#03A9F4', icon: '📊' },
      { name: 'Payment', color: '#00BCD4', icon: '💳' },
      { name: 'Design Patterns', color: '#009688', icon: '🎨' },
      { name: 'Localization', color: '#4CAF50', icon: '🌍' },
      { name: 'Theming', color: '#8BC34A', icon: '🎭' },
    ];

    const svgGroup = document.querySelector("svg g");
    const radius = 240;
    const innerRadius = 140;
    const count = topics.length;

    for (let i = 0; i < count; i++) {
      const angle = (2 * Math.PI / count);
      const startAngle = i * angle;
      const endAngle = (i + 1) * angle;

      // Polar to Cartesian
      const polar = (r, a) => [
        r * Math.cos(a),
        r * Math.sin(a)
      ];

      const [x1, y1] = polar(radius, startAngle);
      const [x2, y2] = polar(radius, endAngle);
      const [ix1, iy1] = polar(innerRadius, endAngle);
      const [ix2, iy2] = polar(innerRadius, startAngle);

      // Path for sector
      const d = `
        M ${ix2} ${iy2}
        A ${innerRadius} ${innerRadius} 0 0 1 ${ix1} ${iy1}
        L ${x2} ${y2}
        A ${radius} ${radius} 0 0 0 ${x1} ${y1}
        Z
      `;

      // Create sector group
      const sector = document.createElementNS("http://www.w3.org/2000/svg", "g");
      sector.classList.add("sector-button");

      // Path shape
      const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
      path.setAttribute("d", d.trim());
      path.setAttribute("fill", "#fff");
      path.setAttribute("stroke", "#ccc");
      path.setAttribute("stroke-width", "1");

      // Add text + icon
      const [tx, ty] = polar((radius + innerRadius) / 2, startAngle + angle / 2);
      const text = document.createElementNS("http://www.w3.org/2000/svg", "foreignObject");
      text.setAttribute("x", tx - 40);
      text.setAttribute("y", ty - 20);
      text.setAttribute("width", "80");
      text.setAttribute("height", "40");

      text.innerHTML = `
        <div class="w-full h-full flex flex-col items-center justify-center text-xs font-semibold pointer-events-none">
          <div style="color:${topics[i].color}; font-size: 20px;">${topics[i].icon}</div>
          <div>${topics[i].name}</div>
        </div>
      `;

      // Colored circle
      const [cx, cy] = polar((radius + 10), startAngle + angle / 2);
      const dot = document.createElementNS("http://www.w3.org/2000/svg", "circle");
      dot.setAttribute("cx", cx);
      dot.setAttribute("cy", cy);
      dot.setAttribute("r", 6);
      dot.setAttribute("fill", topics[i].color);

      sector.appendChild(path);
      sector.appendChild(text);
      sector.appendChild(dot);
      svgGroup.appendChild(sector);
    }
  </script>
</body>
</html>

<?php
?>