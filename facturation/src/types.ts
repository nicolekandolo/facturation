export type Role = 'caissier' | 'manager' | 'super_admin';

export interface User {
  id: string;
  username: string;
  password?: string;
  role: Role;
  name: string;
  isActive: boolean;
}

export interface Product {
  id: string;
  name: string;
  price: number;
  stock: number;
  barcode: string;
  category: string;
  minStockAlert: number;
}

export interface InvoiceItem {
  productId: string;
  name: string;
  price: number;
  quantity: number;
  total: number;
}

export interface Invoice {
  id: string;
  invoiceNumber: string;
  date: string;
  cashierId: string;
  cashierName: string;
  items: InvoiceItem[];
  subtotal: number;
  tax: number;
  total: number;
  paymentMethod: 'cash' | 'card' | 'mobile';
  customerName?: string;
}

export interface SalesReport {
  totalSales: number;
  invoiceCount: number;
  itemsSold: number;
  categoryBreakdown: Record<string, number>;
  salesByMethod: Record<string, number>;
}
