import axios from 'axios';
import { useState, useEffect } from 'react';

const api = axios.create({
  baseURL: "http://127.0.0.1:8000",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

interface Productdata {
  totalrecords: number,
  totpage: number,
  page: number,
  products: Products[]
}

interface Products {
  id: number,
  descriptions: string  
  qty: number,
  unit: string,
  sellprice: number,
  productpicture: string
}

export default function Prodlist() {

  const toDecimal = (number: any) => {
    const formatter = new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
    return formatter.format(number);
  };

    const [page, setPage] = useState<number>(1);
    const [totpage, setTotpage] = useState<number>(0);
    const [totalrecs, setTotalrecs] = useState<number>(0);
    const [message, setMessage] = useState<string>('');

    const [products, setProducts] = useState<Products[]>([]);


    const fetchProducts = async (pg: number) => {
      await api.get<Productdata>(`/api/productlist/${pg}`)
      .then((res: any) => {
        const data: Productdata = res.data;
        setProducts(data.products);
        setTotpage(data.totpage);
        setTotalrecs(data.totalrecords)
        setPage(data.page);
      }, (error: any) => {
        if (error.response) {
          setMessage(error.response.data.message)
        } else {
          setMessage(error.message);
        }
      });      
    };


    useEffect(() => {
      fetchProducts(page);
   },[page]);

    const firstPage = (event: any) => {
        event.preventDefault();            
        fetchProducts(1);
        return;    
      }
    
      const nextPage = (event: any) => {
        event.preventDefault();    
        if (page === totpage) return;
        let pg: number = page;
        pg++;
        setPage(pg)
        fetchProducts(pg);
      }
    
      const prevPage = (event: any) => {
        event.preventDefault();    
        if (page > 1)  setPage(prev => prev - 1);  
      }
    
      const lastPage = (event: any) => {
        event.preventDefault();
        return fetchProducts(totpage);
      }  
  
  return (
    <div className="container">
            <h1 className='text-warning embossed mt-3'>Products List</h1>
            <div className='text-white'>{message}</div>
            <table className="table table-info table-striped table-hover">
            <thead>
                <tr>
                <th className='bg-primary text-white' scope="col">#</th>
                <th className='bg-primary text-white' scope="col">Descriptions</th>
                <th className='bg-primary text-white' scope="col">Qty</th>
                <th className='bg-primary text-white' scope="col">Unit</th>
                <th className='bg-primary text-white' scope="col">Price</th>
                </tr>
            </thead>
            <tbody>

            {products.map((item) => {
            return (
              <tr key={item['id']}>
                 <td>{item['id']}</td>
                 <td>{item['descriptions']}</td>
                 <td>{item['qty']}</td>
                 <td>{item['unit']}</td>
                 <td>&#8369;{toDecimal(item['sellprice'])}</td>
               </tr>
              );
            })}

            </tbody>
            </table>

            <nav aria-label="Page navigation example">
        <ul className="pagination sm">
          <li className="page-item"><a onClick={lastPage} className="page-link sm" href="/#">Last</a></li>
          <li className="page-item"><a onClick={prevPage} className="page-link sm" href="/#">Previous</a></li>
          <li className="page-item"><a onClick={nextPage} className="page-link sm" href="/#">Next</a></li>
          <li className="page-item"><a onClick={firstPage} className="page-link sm" href="/#">First</a></li>
          <li className="page-item page-link text-danger sm">Page&nbsp;{page} of&nbsp;{totpage}</li>

        </ul>
      </nav>
      <div className='text-warning'><strong>Total Records : {totalrecs}</strong></div>
    </div>    
  )
}
