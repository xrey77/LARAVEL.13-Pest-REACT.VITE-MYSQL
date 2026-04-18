import { useEffect, useState } from "react";
import axios from "axios";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

export default function ProductReport() {
  const [pdfUrl, setPdfUrl] = useState<string | null>(null);

  const pdfReport = async () => {

    try {
      
    const res = await api.get('/api/pdfreport', {
      responseType: 'blob'
    });
      console.log(res);

      const fileURL = URL.createObjectURL(res.data);
      setPdfUrl(fileURL);
    } catch (error) {
      console.error("PDF Generation Error:", error);
    }
  };

  useEffect(() => {
    pdfReport();
    return () => {
      if (pdfUrl) URL.revokeObjectURL(pdfUrl);
    };
  }, []);

  return (
    <div style={{ height: '100vh', width: '100%' }}>
       {pdfUrl ? (
        <iframe 
          src={`${pdfUrl}#toolbar=1`}
          width="100%" 
          height="100%" 
          title="Product Report"
          style={{ border: 'none' }}
        />
      ) : (
        <p>Loading report...</p>
      )}
    </div>
  );
}
