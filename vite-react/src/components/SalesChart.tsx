import { useEffect, useRef, useState } from "react";
import { useReactToPrint } from 'react-to-print';
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  BarElement, 
  Title, 
  Tooltip, 
  Legend, 
} from 'chart.js';
import type { ChartOptions } from 'chart.js';

import { Bar } from 'react-chartjs-2';
import type { ChartData } from 'chart.js';
import axios from "axios";

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

const api = axios.create({
  baseURL: "http://localhost:3000/graphql",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

const logo = new Image();
logo.src = '/images/logo.png';

const logoPlugin = {
  id: 'logoPlugin',
  beforeDraw: (chart: any) => {
    if (logo.complete) {
      const { ctx, width } = chart;
      const logoWidth = 150;
      const logoHeight = 30;
      const x = (width - logoWidth) / 2; 
      const y = 10; 
      
      ctx.drawImage(logo, x, y, logoWidth, logoHeight);
    } else {
      logo.onload = () => chart.draw();
    }
  }
};


interface SalesData {
  date: string;
  amount: number;
}


export default function SalesChart() {
  const [message, setMessage] = useState<string>('');

const options: ChartOptions<'bar'> = {
  responsive: true,
  layout: {
    padding: {
      top: 40 
    }
  },
  plugins: {
    legend: { position: 'top' as const },
    title: { 
      display: true,
      text: 'Annual Sales Report',
      padding: {
        top: 10, 
        bottom: 5
      },
      font: {
        size: 24,
        family: 'Arial',
        weight: 'bold',
      }
    },
  },
};
  

  const [chartData, setChartData] = useState<ChartData<'bar'>>({
    labels: [],
    datasets: [],
  });

  
  const chartRef = useRef<HTMLDivElement>(null);

  const handlePrint = useReactToPrint({
    contentRef: chartRef,
    documentTitle: "Sales Chart Report",
  });

  const barChart = async () => {
    const chartQuery = {
      query: `query { showChart { amount date } }`,
    };

    try {
      const res = await api.post('', chartQuery);
      const result = res.data.data?.showChart; 
      
      if (result) {
        const labels = result.map((item: SalesData) => 
          new Date(item.date).toLocaleString('en-US', { month: 'short' })
        );
        const data = result.map((item: SalesData) => item.amount);

        setChartData({
          labels,
          datasets: [{
            label: 'Sales',
            data: data,
            backgroundColor: 'rgba(60, 179, 113)',
          }],
        });
      }
    } catch (error: any) {
      setMessage(error.message);
    }
  };

  useEffect(() => {
    barChart();
  }, []);

  return (
    <div className='container bg-white mt-3 r-corner'>
      {message && <p style={{color: 'red'}}>{message}</p>}
      
      <div className="print-header">
        <h1>Sale Report</h1>
        <p>Generated on: {new Date().toLocaleDateString()}</p>
      </div>

      <div ref={chartRef} style={{ padding: '20px' }}>
        {chartData.datasets.length > 0 ? (
          <Bar options={options} data={chartData} plugins={[logoPlugin]} />
        ) : (
          <p>Loading chart data...</p>
        )}
      </div>
      
      <button className="btn btn-success btn-sm b-print" onClick={() => handlePrint()}>Print Chart</button>

      <style>{`
        .print-header { display: none; text-align: center; margin-bottom: 20px; }
        @media print {
          .print-header { display: block; }
          button { display: none; }
          canvas { max-width: 100% !important; height: auto !important; }
        }      
      `}</style>
    </div>
  );
}
