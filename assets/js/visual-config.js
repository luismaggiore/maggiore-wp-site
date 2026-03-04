const VISUAL_CONFIG = {
aurora: {
enabled: true,
barCount: 18,
palette: [
'#00d0ff',
'#00ffff',
'#00ff99',
'#00ff91',
'#041e59',
'#00b7ff'
],
amplitudeX: 615,
amplitudeScale: 0.2,
speedX: 0.07,
speedScale: 0.07,
speedMultiplier: 1.1,
speedJitter: 0.15,
amplitudeJitter: 0.2,
blur: {
min: 25,
max: 160,
responsive: true
},
opacity: {
min: 0.55,
max: 0.9
},
targetFPS: 60,
renderThrottle: 16
},
constellation: {
enabled: true,
maxDistance: 220,
maxNeighbors: 2,
lineStyle: {
stroke: 'gray',
strokeWidth: 0.9,
opacity: 0.8
},
movementThreshold: 1,
states: {
robinHood: [
{ cx: 300, cy: 250 },
{ cx: 240, cy: 130 },
{ cx: 300, cy: 80 },
{ cx: 300, cy: 410 },
{ cx: 140, cy: 510 },
{ cx: 360, cy: 130 },
{ cx: 460, cy: 510 }
],
inteligencia: [
{ cx: 340, cy: 260 },
{ cx: 280, cy: 140 },
{ cx: 340, cy: 90 },
{ cx: 340, cy: 420 },
{ cx: 180, cy: 520 },
{ cx: 400, cy: 140 },
{ cx: 500, cy: 520 }
],
flexible: [
{ cx: 50, cy: 290 },
{ cx: 200, cy: 390 },
{ cx: 300, cy: 290 },
{ cx: 150, cy: 190 },
{ cx: 450, cy: 390 },
{ cx: 400, cy: 190 },
{ cx: 550, cy: 290 }
]
}
},
scrollAnimations: {
enabled: true,
smoother: {
enabled: false,
smooth: 1.5,
effects: true
},
constellationMorph: {
duration: 1,
ease: 'none',
stagger: 0.03
},
parallax: {
enabled: true,
intensity: 150
},
entrance: {
duration: 0.6,
stagger: 0.4,
ease: 'power1.out'
}
},
performance: {
targetFPS: 55,
enableMonitoring: true,
monitorInterval: 1000,
autoDegradeThreshold: 30,
pauseWhenHidden: true,
respectReducedMotion: true,
detectLowEnd: true,
deviceSettings: {
desktop: {
auroraCount: 18,
constellationEnabled: true,
highQuality: true
},
tablet: {
auroraCount: 12,
constellationEnabled: true,
highQuality: false
},
mobile: {
auroraCount: 6,
constellationEnabled: false,
highQuality: false
}
}
},
debug: {
showFPS: false,
logEffects: true,
logPerformance: false,
logAnimations: false,
showDebugPanel: false,
showPerformanceWarnings: true,
colors: {
info: '#00d0ff',
warning: '#ffa500',
error: '#ff0000',
success: '#00ff99'
}
},
features: {
aurora: true,
constellation: true,
parallax: true,
textAnimations: true,
testimonialSlider: true
}
};
VISUAL_CONFIG.getDeviceType = function() {
const width = window.innerWidth;
if (width < 768) return 'mobile';
if (width < 1024) return 'tablet';
return 'desktop';
};
VISUAL_CONFIG.isLowEndDevice = function() {
const cores = navigator.hardwareConcurrency || 2;
const memory = navigator.deviceMemory || 4;
const isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
return cores <= 2 || memory <= 4 || isMobile;
};
VISUAL_CONFIG.getOptimalConfig = function() {
const deviceType = this.getDeviceType();
const isLowEnd = this.isLowEndDevice();
const config = JSON.parse(JSON.stringify(this));
if (isLowEnd || deviceType === 'mobile') {
config.aurora.barCount = 6;
config.aurora.targetFPS = 30;
config.constellation.enabled = false;
config.scrollAnimations.smoother.enabled = false;
} else if (deviceType === 'tablet') {
config.aurora.barCount = 12;
config.aurora.targetFPS = 45;
}
return config;
};
if (typeof window !== 'undefined') {
window.VISUAL_CONFIG = VISUAL_CONFIG;
if (VISUAL_CONFIG.debug.logEffects) {
console.log('%c🎨 Visual Config Loaded',
'color: #00d0ff; font-weight: bold; font-size: 14px;');
console.log('Device:', VISUAL_CONFIG.getDeviceType());
console.log('Low-end device:', VISUAL_CONFIG.isLowEndDevice());
}
}
if (typeof module !== 'undefined' && module.exports) {
module.exports = VISUAL_CONFIG;
}