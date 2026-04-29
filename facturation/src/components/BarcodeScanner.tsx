import React, { useEffect, useRef, useState } from 'react';
import { Camera, X, RefreshCw, AlertCircle } from 'lucide-react';
import { useApp } from '../context/AppContext';

interface BarcodeScannerProps {
  onScan: (barcode: string) => void;
  onClose: () => void;
}

declare global {
  interface Window {
    BarcodeDetector: any;
  }
}

export const BarcodeScanner: React.FC<BarcodeScannerProps> = ({ onScan, onClose }) => {
  const { products } = useApp();
  const videoRef = useRef<HTMLVideoElement>(null);
  const [error, setError] = useState<string>('');
  const [devices, setDevices] = useState<MediaDeviceInfo[]>([]);
  const [selectedDevice, setSelectedDevice] = useState<string>('');
  const [isSearching, setIsSearching] = useState<boolean>(false);
  const [isSupported, setIsSupported] = useState<boolean>(true);
  const scanIntervalRef = useRef<number | null>(null);

  useEffect(() => {
    if (!('BarcodeDetector' in window)) {
      setIsSupported(false);
    }
    
    const getCameras = async () => {
      try {
        const mediaDevices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = mediaDevices.filter(device => device.kind === 'videoinput');
        setDevices(videoDevices);
        if (videoDevices.length > 0) {
          // Select back camera if possible
          const backCam = videoDevices.find(d => d.label.toLowerCase().includes('back'));
          setSelectedDevice(backCam ? backCam.deviceId : videoDevices[0].deviceId);
        }
      } catch (err) {
        setError('Impossible d\'accéder aux caméras.');
      }
    };

    getCameras();

    return () => {
      stopCamera();
    };
  }, []);

  useEffect(() => {
    if (selectedDevice) {
      startCamera();
    }
  }, [selectedDevice]);

  const startCamera = async () => {
    setError('');
    stopCamera();

    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: {
          deviceId: selectedDevice ? { exact: selectedDevice } : undefined,
          width: { ideal: 1280 },
          height: { ideal: 720 },
          facingMode: 'environment'
        }
      });

      if (videoRef.current) {
        videoRef.current.srcObject = stream;
        videoRef.current.play();
        setIsSearching(true);
        startScanning();
      }
    } catch (err: any) {
      setError(`Erreur d'accès caméra : ${err.message || err}`);
    }
  };

  const stopCamera = () => {
    if (scanIntervalRef.current) {
      clearInterval(scanIntervalRef.current);
      scanIntervalRef.current = null;
    }
    setIsSearching(false);

    if (videoRef.current && videoRef.current.srcObject) {
      const stream = videoRef.current.srcObject as MediaStream;
      const tracks = stream.getTracks();
      tracks.forEach(track => track.stop());
      videoRef.current.srcObject = null;
    }
  };

  const startScanning = () => {
    if (!('BarcodeDetector' in window)) return;

    const barcodeDetector = new window.BarcodeDetector({
      formats: ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39', 'code_93', 'itf']
    });

    scanIntervalRef.current = window.setInterval(async () => {
      if (videoRef.current && videoRef.current.readyState >= 2) {
        try {
          const barcodes = await barcodeDetector.detect(videoRef.current);
          if (barcodes && barcodes.length > 0) {
            const code = barcodes[0].rawValue;
            onScan(code);
            stopCamera();
            onClose();
          }
        } catch (e) {
          // Detection error
        }
      }
    }, 300);
  };

  const handleDeviceChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setSelectedDevice(e.target.value);
  };

  // Simulation mode for environments without native BarcodeDetector or real camera
  const triggerSimulation = (barcode: string) => {
    onScan(barcode);
    stopCamera();
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-slate-900 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl border border-slate-800">
        <div className="p-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <div className="flex items-center space-x-2 text-indigo-400">
            <Camera size={20} />
            <h3 className="text-white font-bold">Lecteur Code-barres</h3>
          </div>
          <button 
            onClick={() => { stopCamera(); onClose(); }}
            className="p-1 rounded-full text-slate-400 hover:text-white hover:bg-slate-800 transition-all"
          >
            <X size={20} />
          </button>
        </div>

        <div className="relative aspect-video bg-black flex items-center justify-center">
          {error ? (
            <div className="text-red-400 text-center p-4">
              <AlertCircle className="mx-auto mb-2 text-red-500" size={32} />
              <p className="text-sm">{error}</p>
            </div>
          ) : (
            <video 
              ref={videoRef} 
              className="w-full h-full object-cover"
              playsInline 
            />
          )}

          {isSearching && (
            <div className="absolute inset-0 pointer-events-none flex flex-col items-center justify-center">
              <div className="border-2 border-indigo-500 w-64 h-40 rounded-xl relative shadow-[0_0_15px_rgba(99,102,241,0.5)]">
                <div className="absolute top-0 bottom-0 left-0 right-0 border border-indigo-400/30 animate-pulse bg-indigo-500/10"></div>
                {/* Scanning line animation */}
                <div className="absolute top-0 left-0 right-0 h-0.5 bg-red-500 animate-bounce"></div>
              </div>
              <span className="text-white text-xs mt-4 bg-slate-950/80 px-3 py-1 rounded-full backdrop-blur-sm">
                Placez le code-barres au centre du cadre
              </span>
            </div>
          )}
        </div>

        <div className="p-4 bg-slate-950 space-y-4">
          {devices.length > 1 && (
            <div>
              <label className="block text-xs font-semibold text-slate-400 mb-1">
                Choisir la caméra
              </label>
              <div className="relative">
                <select 
                  value={selectedDevice} 
                  onChange={handleDeviceChange}
                  className="w-full bg-slate-800 text-slate-200 text-sm p-2 rounded-lg border border-slate-700 focus:outline-none"
                >
                  {devices.map(device => (
                    <option key={device.deviceId} value={device.deviceId}>
                      {device.label || `Caméra ${device.deviceId.slice(0, 5)}`}
                    </option>
                  ))}
                </select>
                <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                  <RefreshCw size={14} className="animate-spin" />
                </div>
              </div>
            </div>
          )}

          {!isSupported && (
            <div className="bg-amber-500/10 border border-amber-500/30 text-amber-300 p-3 rounded-lg text-xs flex items-start space-x-2">
              <AlertCircle size={16} className="shrink-0 mt-0.5" />
              <span>
                <strong>Note:</strong> Le scanner natif n'est pas pris en charge par ce navigateur.
                Sélectionnez un produit ci-dessous pour tester.
              </span>
            </div>
          )}

          <div className="border-t border-slate-800 pt-3">
            <p className="text-xs font-semibold text-slate-400 mb-2">Simulateur de scan (Produits disponibles) :</p>
            <div className="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-1">
              {products.map(p => (
                <button
                  key={p.id}
                  onClick={() => triggerSimulation(p.barcode)}
                  className="px-2 py-1 bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white text-xs rounded transition-all flex flex-col items-start border border-slate-700"
                >
                  <span className="font-semibold">{p.name}</span>
                  <span className="opacity-70 text-[10px]">{p.barcode}</span>
                </button>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
