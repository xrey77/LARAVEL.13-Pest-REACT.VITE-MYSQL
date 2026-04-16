import axios from 'axios';
import { useState } from 'react';

const api = axios.create({
  baseURL: "http://localhost:3000/graphql",
  headers: {'Accept': 'application/json',
            'Content-Type': 'application/json'}
})

const toDecimal = (number: any) => {
  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
  return formatter.format(number);
};

export default function Prodsearch() {
  const [prodsearch, setProdsearch] = useState<[]>([]);
  const [message, setMessage] = useState<string>('');
  const [page, setPage] = useState<number>(1);
  const [totpage, setTotpage] = useState<number>(0);
  const [searchkey, setSearchkey] = useState<string>('');
  const [totalrecs, setTotalrecs] = useState<number>(0);


  const getProdsearch = async (event: any) => {
      event.preventDefault();
      setMessage("please wait .");
      getProdPage(page);
      setTimeout(() => {
        setMessage('');
      }, 1000);

  }

  const getProdPage = async (page: number) => {
    setMessage("please wait .");
        const searchQuery = {
            query: `
                query SearchProducts($page: Int!, $key: String!) {
                    findProductsByDescriptions(page: $page, key: $key) {
                        products {
                            id
                            descriptions
                            qty
                            unit
                            sellprice
                            productpicture
                        }
                        page
                        totpage
                        totalrecords
                    }
                }
            `,
            variables: { page: page, key: searchkey } 
        };

        try {
            const res = await api.post('', searchQuery);
            const result = res.data.data?.findProductsByDescriptions; 
            if (result) {
                setProdsearch(result.products);
                setTotalrecs(result.totalrecords);
                setTotpage(result.totpage);

            } else {

            setTimeout(() => {
              setMessage('');
              setTotpage(0);
              setTotalrecs(0);
              setProdsearch([]);
            }, 3000);

            }
        } catch (error: any) {
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            setMessage(errorMsg);
            setTimeout(() => {
              setMessage('');
              setTotpage(0);
            }, 1000);
        }
}

  const firstPage = (event: any) => {
    event.preventDefault();    
    return getProdPage(1);
  }

  const nextPage = (event: any) => {
    event.preventDefault();    
    if (page === totpage) return;
    let pg: number = page;
    pg++;
    setPage(pg)
    getProdPage(pg);

  }

  const prevPage = (event: any) => {
    event.preventDefault();    
    if (page > 1)  setPage(prev => prev - 1);  
    return getProdPage(page);
  }

  const lastPage = (event: any) => {
    event.preventDefault();
    return getProdPage(totpage);
  }  
   
return (
  <div className="container mb-10">
      <h2 className='text-warning embossed mt-3'>Products Search</h2>

      <form className="row g-3" onSubmit={getProdsearch} autoComplete='off'>
          <div className="col-auto">
            <input type="text" required className="form-control-sm" value={searchkey} onChange={e => setSearchkey(e.target.value)} placeholder="enter Product keyword"/>
            <div className='searcMsg text-warning'>{message}</div>
          </div>
          <div className="col-auto">
            <button type="submit" className="btn btn-primary btn-sm mb-3">search</button>
          </div>

      </form>
      <div className="container mb-9">
        <div className="card-group">
      {prodsearch.map((item) => {
              return (
              <div className='col-md-4'>
              <div key={item['id']} className="card mx-3 mt-3">
                  <img src={`http://localhost:3000/products/${item['productpicture']}`} className="card-img-top product-size" alt=""/>
                  <div className="card-body">
                    <h5 className="card-title">Descriptions</h5>
                    <p className="card-text desc-h">{item['descriptions']}</p>
                  </div>
                  <div className="card-footer">
                    <p className="card-text text-danger"><span className="text-dark">PRICE :</span>&nbsp;<strong>&#8369;{toDecimal(item['sellprice'])}</strong></p>
                  </div>  
              </div>
              
              </div>
        );    
      })}
        </div>          
        {
          totpage > 1 ? 
            <nav aria-label="Page navigation example">
              <ul className="pagination sm mt-3">
                <li className="page-item"><a onClick={lastPage} className="page-link sm" href="/#">Last</a></li>
                <li className="page-item"><a onClick={prevPage} className="page-link sm" href="/#">Previous</a></li>
                <li className="page-item"><a onClick={nextPage} className="page-link sm" href="/#">Next</a></li>
                <li className="page-item"><a onClick={firstPage} className="page-link sm" href="/#">First</a></li>
                <li className="page-item page-link text-danger sm">Page&nbsp;{page} of&nbsp;{totpage}</li>
              </ul>
            </nav>
        :
        null
        }
        <div className='text-warning mt-2'><strong>&nbsp;&nbsp;&nbsp;&nbsp;Total Records : {totalrecs}</strong></div>

        <br/><br/><br/>
      </div>
  </div>  
  )
}

