import React, { createContext, useContext, useState, useEffect } from 'react';
import { User, Product, Invoice } from '../types';

interface AppContextType {
  currentUser: User | null;
  users: User[];
  products: Product[];
  invoices: Invoice[];
  login: (username: string, password: string) => boolean;
  logout: () => void;
  addUser: (user: Omit<User, 'id'>) => void;
  updateUser: (user: User) => void;
  deleteUser: (id: string) => void;
  addProduct: (product: Omit<Product, 'id'>) => void;
  updateProduct: (product: Product) => void;
  deleteProduct: (id: string) => void;
  adjustStock: (id: string, quantity: number) => void;
  createInvoice: (items: any[], paymentMethod: any, customerName?: string) => Invoice;
}

const defaultUsers: User[] = [
  { id: '1', username: 'admin', password: 'admin123', role: 'super_admin', name: 'Super Administrateur', isActive: true },
  { id: '2', username: 'manager', password: 'manager123', role: 'manager', name: 'Manager Magasin', isActive: true },
  { id: '3', username: 'caissier', password: 'caissier123', role: 'caissier', name: 'Caissier Principal', isActive: true }
];

const defaultProducts: Product[] = [
  { id: 'p1', name: 'Bouteille d\'eau 1.5L', price: 1.50, stock: 120, barcode: '3017620422003', category: 'Boissons', minStockAlert: 20 },
  { id: 'p2', name: 'Pain de Mie complet', price: 2.20, stock: 45, barcode: '5449000000996', category: 'Alimentation', minStockAlert: 10 },
  { id: 'p3', name: 'Savon liquide 500ml', price: 3.50, stock: 60, barcode: '7613034947231', category: 'Hygiène', minStockAlert: 15 },
  { id: 'p4', name: 'Paquet de Pâtes 500g', price: 1.10, stock: 200, barcode: '1234567890128', category: 'Alimentation', minStockAlert: 30 },
  { id: 'p5', name: 'Lessive Liquide 3L', price: 12.90, stock: 15, barcode: '3123456789012', category: 'Entretien', minStockAlert: 5 },
  { id: 'p6', name: 'Jus d\'Orange 1L', price: 2.75, stock: 80, barcode: '3256789012345', category: 'Boissons', minStockAlert: 15 }
];

const AppContext = createContext<AppContextType | undefined>(undefined);

export const AppProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [currentUser, setCurrentUser] = useState<User | null>(() => {
    const stored = localStorage.getItem('fact_current_user');
    return stored ? JSON.parse(stored) : null;
  });

  const [users, setUsers] = useState<User[]>(() => {
    const stored = localStorage.getItem('fact_users');
    return stored ? JSON.parse(stored) : defaultUsers;
  });

  const [products, setProducts] = useState<Product[]>(() => {
    const stored = localStorage.getItem('fact_products');
    return stored ? JSON.parse(stored) : defaultProducts;
  });

  const [invoices, setInvoices] = useState<Invoice[]>(() => {
    const stored = localStorage.getItem('fact_invoices');
    return stored ? JSON.parse(stored) : [];
  });

  useEffect(() => {
    localStorage.setItem('fact_users', JSON.stringify(users));
  }, [users]);

  useEffect(() => {
    localStorage.setItem('fact_products', JSON.stringify(products));
  }, [products]);

  useEffect(() => {
    localStorage.setItem('fact_invoices', JSON.stringify(invoices));
  }, [invoices]);

  useEffect(() => {
    if (currentUser) {
      localStorage.setItem('fact_current_user', JSON.stringify(currentUser));
    } else {
      localStorage.removeItem('fact_current_user');
    }
  }, [currentUser]);

  const login = (username: string, password: string) => {
    const user = users.find(u => u.username === username && u.password === password && u.isActive);
    if (user) {
      setCurrentUser(user);
      return true;
    }
    return false;
  };

  const logout = () => {
    setCurrentUser(null);
  };

  const addUser = (userData: Omit<User, 'id'>) => {
    const newUser: User = {
      ...userData,
      id: `u-${Date.now()}`
    };
    setUsers([...users, newUser]);
  };

  const updateUser = (updated: User) => {
    setUsers(users.map(u => u.id === updated.id ? updated : u));
    if (currentUser?.id === updated.id) {
      setCurrentUser(updated);
    }
  };

  const deleteUser = (id: string) => {
    setUsers(users.map(u => u.id === id ? { ...u, isActive: false } : u));
  };

  const addProduct = (prodData: Omit<Product, 'id'>) => {
    const newProd: Product = {
      ...prodData,
      id: `p-${Date.now()}`
    };
    setProducts([...products, newProd]);
  };

  const updateProduct = (updated: Product) => {
    setProducts(products.map(p => p.id === updated.id ? updated : p));
  };

  const deleteProduct = (id: string) => {
    setProducts(products.filter(p => p.id !== id));
  };

  const adjustStock = (id: string, quantity: number) => {
    setProducts(prev => prev.map(p => {
      if (p.id === id) {
        const newStock = Math.max(0, p.stock + quantity);
        return { ...p, stock: newStock };
      }
      return p;
    }));
  };

  const createInvoice = (items: any[], paymentMethod: any, customerName?: string) => {
    const subtotal = items.reduce((sum, item) => sum + item.total, 0);
    const tax = subtotal * 0.20; // 20% TVA
    const total = subtotal + tax;
    
    // Auto-decrement product stock
    items.forEach(item => {
      adjustStock(item.productId, -item.quantity);
    });

    const newInvoice: Invoice = {
      id: `inv-${Date.now()}`,
      invoiceNumber: `FAC-${new Date().getFullYear()}${(invoices.length + 1).toString().padStart(4, '0')}`,
      date: new Date().toISOString(),
      cashierId: currentUser?.id || 'unknown',
      cashierName: currentUser?.name || 'Inconnu',
      items,
      subtotal,
      tax,
      total,
      paymentMethod,
      customerName
    };

    setInvoices(prev => [newInvoice, ...prev]);
    return newInvoice;
  };

  return (
    <AppContext.Provider value={{
      currentUser,
      users,
      products,
      invoices,
      login,
      logout,
      addUser,
      updateUser,
      deleteUser,
      addProduct,
      updateProduct,
      deleteProduct,
      adjustStock,
      createInvoice
    }}>
      {children}
    </AppContext.Provider>
  );
};

export const useApp = () => {
  const context = useContext(AppContext);
  if (context === undefined) {
    throw new Error('useApp must be used within an AppProvider');
  }
  return context;
};
