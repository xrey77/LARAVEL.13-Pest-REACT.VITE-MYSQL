import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { HashRouter, Route, Routes } from 'react-router-dom'
import { Suspense, lazy } from 'react';


// import Header from './components/Header.tsx'
// import About from './components/About.tsx'
// import Home from './components/Home.tsx'
// import Contact from './components/Contact.tsx';
// import Prodlist from './components/Prodlist.tsx';
// import Prodcatalog from './components/Prodcatalog.tsx';
// import Prodsearch from './components/Prodsearch.tsx';
// import Profile from './components/Profile.tsx';
// import ProductReport from './components/ProductReport.tsx';
// import SalesChart from './components/SalesChart.tsx';

const Header= lazy(() => import('./components/Header.tsx'));
const Home= lazy(() => import('./components/Home.tsx'));
const About = lazy(() => import('./components/About.tsx'));
const Contact = lazy(() => import('./components/Contact.tsx'));
const Profile = lazy(() => import('./components/Profile.tsx'));
const Prodlist = lazy(() => import('./components/Prodlist.tsx'));
const Prodcatalog = lazy(() => import('./components/Prodcatalog.tsx'));
const Prodsearch = lazy(() => import('./components/Prodsearch.tsx'));
const ProductReport = lazy(() => import('./components/ProductReport.tsx'));
const SalesChart = lazy(() => import('./components/SalesChart.tsx'));

import './App.css'

function App() {
  return (
    <HashRouter>
      <Header/>
      <Suspense fallback={<div className="container p-5">Loading...</div>}>

        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/about" element={<About />} />
          <Route path="/contact" element={<Contact />} />
          <Route path="/profile" element={<Profile />} />
          <Route path="/productlist" element={<Prodlist />} />
          <Route path="/productcatalog" element={<Prodcatalog />} />
          <Route path="/productsearch" element={<Prodsearch />} />
          <Route path="/productreport" element={<ProductReport />} />
          <Route path="/saleschart" element={<SalesChart />} />
        </Routes>

      </Suspense>        
    </HashRouter>    
  )
}

export default App
