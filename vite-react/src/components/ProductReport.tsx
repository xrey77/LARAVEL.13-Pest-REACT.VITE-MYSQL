import { useEffect, useState } from "react";
import axios from "axios";

export default function ProductReport() {
  const [pdfUrl, setPdfUrl] = useState<string | null>(null);

  const pdfReport = async () => {
    const pdfQuery = { query: `query { generateProductPdf }` };

    try {
      const res = await axios.post("http://localhost:3000/graphql", pdfQuery);
      const base64Data = res.data.data?.generateProductPdf;

      if (base64Data) {

        const cleanBase64 = base64Data.replace(/^data:application\/pdf;base64,/, '').trim();
        let paddedBase64 = cleanBase64;
        while (paddedBase64.length % 4 !== 0) {
          paddedBase64 += '=';
        }

        const byteCharacters = atob(paddedBase64);
        const byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i);
        }

        const byteArray = new Uint8Array(byteNumbers);
        const blob = new Blob([byteArray], { type: 'application/pdf' });
        const fileURL = URL.createObjectURL(blob);
        
        setPdfUrl(fileURL);
      }
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
